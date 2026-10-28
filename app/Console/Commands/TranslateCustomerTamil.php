<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Customer;

class TranslateCustomerTamil extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'customers:translate-tamil 
                            {--preview : Preview translation output without updating database}
                            {--limit=5 : Number of customers to process in preview mode}
                            {--force : Overwrite existing Tamil values}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Auto-translate customer name, place, and delivery address to Tamil using phonetic transliteration';

    // In-memory cache for translated words to minimize API calls and speed up execution
    protected $wordCache = [];

    /**
     * Execute the console command.
     */
    public function handle()
    {
        set_time_limit(0);
        ini_set('max_execution_time', '0');

        $preview = $this->option('preview');
        $limit = (int) $this->option('limit');
        $force = $this->option('force');

        $query = Customer::with(['place', 'city']);

        if (!$force) {
            $query->where(function ($q) {
                $q->whereNull('tamil_name')->orWhere('tamil_name', '')
                  ->orWhereNull('tamil_place')->orWhere('tamil_place', '')
                  ->orWhereNull('tamil_address')->orWhere('tamil_address', '');
            });
        }

        $totalCount = $query->count();

        if ($totalCount === 0) {
            $this->info("All customers already have Tamil details!");
            return 0;
        }

        if ($preview) {
            $this->info("=== PREVIEW MODE (Showing first {$limit} customers, NO database changes) ===");
            $customers = $query->take($limit)->get();

            $tableData = [];
            foreach ($customers as $c) {
                $engName = $c->name ?? '';
                $tamName = $this->transliterate($engName);

                $engPlace = $c->place->place_name ?? $c->city->city_name ?? '';
                $tamPlace = $this->transliterate($engPlace);

                $addressParts = array_filter([$c->address_line_1, $c->address_line_2, $c->address_line_3]);
                $engAddress = implode(', ', $addressParts);
                $tamAddress = $this->transliterate($engAddress);

                $tableData[] = [$c->id, 'Name', $engName, $tamName];
                $tableData[] = [$c->id, 'Place', $engPlace, $tamPlace];
                $tableData[] = [$c->id, 'Address', $engAddress, $tamAddress];
                $tableData[] = ['', '------------------', '------------------', '------------------'];
            }

            $this->table(['ID', 'Field', 'English', 'Tamil Preview'], $tableData);
            $this->info("\nTotal customers pending translation: {$totalCount}");
            $this->info("To execute live update, run: php artisan customers:translate-tamil");
            return 0;
        }

        $this->info("Starting live Tamil translation for {$totalCount} customers...");
        $customers = $query->get();
        $bar = $this->output->createProgressBar($customers->count());
        $bar->start();

        $updatedCount = 0;
        foreach ($customers as $c) {
            $updates = [];

            // 1. Tamil Name
            if ($force || empty($c->tamil_name)) {
                $updates['tamil_name'] = $this->transliterate($c->name ?? '');
            }

            // 2. Tamil Place
            if ($force || empty($c->tamil_place)) {
                $placeText = $c->place->place_name ?? $c->city->city_name ?? '';
                $updates['tamil_place'] = $this->transliterate($placeText);
            }

            // 3. Tamil Address
            if ($force || empty($c->tamil_address)) {
                $addressParts = array_filter([$c->address_line_1, $c->address_line_2, $c->address_line_3]);
                $fullAddress = implode(', ', $addressParts);
                $updates['tamil_address'] = $this->transliterate($fullAddress);
            }

            if (!empty($updates)) {
                Customer::where('id', $c->id)->update($updates);
                $updatedCount++;
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("Successfully updated {$updatedCount} customers with Tamil details!");

        return 0;
    }

    /**
     * Phonetic transliteration into Tamil preserving numbers and punctuation.
     */
    protected function transliterate($text)
    {
        if (empty(trim($text))) {
            return '';
        }

        // Tokenize text into words, numbers, and delimiters
        $tokens = preg_split('/(\s+|[0-9]+|[^a-zA-Z0-9\s]+)/', $text, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
        $result = [];

        foreach ($tokens as $token) {
            if (preg_match('/^[a-zA-Z]+$/', $token)) {
                $wordUpper = strtoupper($token);

                if (isset($this->wordCache[$wordUpper])) {
                    $result[] = $this->wordCache[$wordUpper];
                    continue;
                }

                $translatedWord = $this->fetchPhoneticWord($token);
                $this->wordCache[$wordUpper] = $translatedWord;
                $result[] = $translatedWord;
            } else {
                $result[] = $token;
            }
        }

        return implode('', $result);
    }

    /**
     * Call Google Input Tools API for a single English word.
     */
    protected function fetchPhoneticWord($word)
    {
        $url = "https://inputtools.google.com/request?text=" . urlencode($word) . "&itc=ta-t-i0-und&num=1";
        
        $context = stream_context_create([
            'http' => [
                'timeout' => 4,
                'ignore_errors' => true,
            ]
        ]);

        $response = @file_get_contents($url, false, $context);
        if ($response) {
            $json = json_decode($response, true);
            if (isset($json[0]) && $json[0] === 'SUCCESS' && isset($json[1][0][1][0])) {
                return $json[1][0][1][0];
            }
        }

        return $word;
    }
}

