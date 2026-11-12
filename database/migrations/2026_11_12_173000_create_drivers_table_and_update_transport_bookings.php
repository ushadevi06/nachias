<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('drivers')) {
            Schema::create('drivers', function (Blueprint $table) {
                $table->id();
                $table->string('driver_name', 150)->index();
                $table->decimal('loading_unloading_amount', 14, 2)->default(0);
                $table->string('status', 20)->default('Active')->index();
                $table->unsignedBigInteger('created_by')->nullable();
                $table->unsignedBigInteger('updated_by')->nullable();
                $table->timestamps();
                $table->softDeletes();
            });
        }

        if (Schema::hasTable('transport_bookings')) {
            Schema::table('transport_bookings', function (Blueprint $table) {
                if (!Schema::hasColumn('transport_bookings', 'driver_id')) {
                    $table->unsignedBigInteger('driver_id')->nullable()->after('delivery_agent_name')->index();
                }
                if (!Schema::hasColumn('transport_bookings', 'loading_unloading_amount')) {
                    $table->decimal('loading_unloading_amount', 14, 2)->default(0)->after('lr_price');
                }
            });

            // Sync existing unique outside driver names into Drivers master for easy amount setup
            $existingNames = DB::table('transport_bookings')
                ->whereNotNull('delivery_agent_name')
                ->where('delivery_agent_name', '!=', '')
                ->distinct()
                ->pluck('delivery_agent_name');

            foreach ($existingNames as $rawName) {
                $name = trim((string) $rawName);
                if ($name === '') {
                    continue;
                }
                $driverId = DB::table('drivers')
                    ->whereNull('deleted_at')
                    ->whereRaw('LOWER(driver_name) = ?', [strtolower($name)])
                    ->value('id');

                if (!$driverId) {
                    $driverId = DB::table('drivers')->insertGetId([
                        'driver_name' => $name,
                        'loading_unloading_amount' => 0,
                        'status' => 'Active',
                        'created_by' => 1,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }

                DB::table('transport_bookings')
                    ->where('delivery_agent_name', $rawName)
                    ->whereNull('driver_id')
                    ->update(['driver_id' => $driverId]);
            }
        }

        if (Schema::hasTable('permissions')) {
            $perms = [
                ['action' => 'create', 'label' => 'Create Drivers', 'name' => 'create drivers'],
                ['action' => 'edit',   'label' => 'Edit Drivers',   'name' => 'edit drivers'],
                ['action' => 'delete', 'label' => 'Delete Drivers', 'name' => 'delete drivers'],
                ['action' => 'view',   'label' => 'View Drivers',   'name' => 'view drivers'],
            ];
            foreach ($perms as $p) {
                $exists = DB::table('permissions')->where('name', $p['name'])->where('guard_name', 'web')->exists();
                if (!$exists) {
                    DB::table('permissions')->insert([
                        'module' => 'drivers',
                        'action' => $p['action'],
                        'label' => $p['label'],
                        'name' => $p['name'],
                        'guard_name' => 'web',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('transport_bookings')) {
            Schema::table('transport_bookings', function (Blueprint $table) {
                foreach (['loading_unloading_amount', 'driver_id'] as $col) {
                    if (Schema::hasColumn('transport_bookings', $col)) {
                        $table->dropColumn($col);
                    }
                }
            });
        }

        if (Schema::hasTable('permissions')) {
            DB::table('permissions')->where('module', 'drivers')->delete();
        }

        Schema::dropIfExists('drivers');
    }
};

