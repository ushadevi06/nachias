<?php

namespace App\Services\RAG;

use Illuminate\Support\Facades\Route;

class CodeRouteMenuExtractor
{
    /**
     * Extract menu and route knowledge chunks from topbar and routes.
     *
     * @return array<int, array>
     */
    public function extract(): array
    {
        $topbarPath = resource_path('views/layouts/topbar.blade.php');
        $chunks = [];

        // Collect registered route URIs to dynamically resolve /add endpoints across all ERP screens
        $registeredRouteUris = [];
        try {
            foreach (Route::getRoutes() as $route) {
                $registeredRouteUris['/' . ltrim($route->uri(), '/')] = true;
            }
        } catch (\Throwable $e) {
            // Fallback if routes fail to load
        }

        if (file_exists($topbarPath)) {
            $content = file_get_contents($topbarPath);
            $menuChunks = $this->parseTopbarMenu($content, $registeredRouteUris);
            $chunks = array_merge($chunks, $menuChunks);
        }

        $routeChunks = $this->parseRegisteredRoutes($chunks);
        $chunks = array_merge($chunks, $routeChunks);

        return $chunks;
    }

    /**
     * Resolve the Add / Create route for a given menu screen path.
     */
    public function resolveAddUrl(string $path, array $registeredRouteUris = []): ?string
    {
        $clean = '/' . ltrim($path, '/');

        // Explicit known overrides
        $explicit = [
            '/stock_consumables_returns' => '/add_stock_consumables_return',
            '/monthly_payroll' => '/add_monthly_payroll',
            '/leave' => '/add_leave',
            '/payroll_reports' => '/add_payroll_report',
            '/ticket_management' => '/ticket_management/add',
        ];

        if (isset($explicit[$clean])) {
            return $explicit[$clean];
        }

        // Standard candidates
        $candidates = [
            $clean . '/add',
            $clean . '/add/{id?}',
            $clean . '/add/{id}',
            $clean . '/create',
            '/add' . $clean,
            '/add_' . ltrim($clean, '/'),
        ];

        // Singular candidate (e.g. /warehouses -> /warehouse/add)
        if (str_ends_with($clean, 's')) {
            $singular = rtrim($clean, 's');
            $candidates[] = $singular . '/add';
            $candidates[] = $singular . '/add/{id?}';
            $candidates[] = '/add_' . ltrim($singular, '/');
        }

        foreach ($candidates as $cand) {
            if (isset($registeredRouteUris[$cand])) {
                return str_replace(['/{id?}', '/{id}'], '', $cand);
            }
        }

        return null;
    }

    /**
     * Parse topbar.blade.php menu items with accurate hierarchical breadcrumbs and deduplication.
     */
    protected function parseTopbarMenu(string $html, array $registeredRouteUris = []): array
    {
        $chunks = [];
        $lines = explode("\n", $html);

        $currentTop = 'Master';
        $currentSub = null;

        $topMenuNames = [
            'Master', 'Employees', 'Purchase', 'Store',
            'Production', 'Sales', 'Billing', 'Manage Payments',
            'Emp. Payroll & Attendance', 'System Utility', 'Reports'
        ];

        $subGroupNames = [
            'Logistics Master', 'Tailoring Specification', 'Production Master',
            'Warehouse Master', 'Parties', 'Item Setup', 'Purchase Reports'
        ];

        $seenUrls = [];
        $totalLines = count($lines);

        for ($i = 0; $i < $totalLines; $i++) {
            $line = $lines[$i];

            // 1. Detect comment headers (e.g. <!-- Employees -->, <!-- Master -->, {{-- Production --}})
            if (preg_match('/(?:<!--|{{\s*--)\s*([A-Za-z0-9\s&.,\/-]+)\s*(?:-->|--\s*}})/', $line, $cm)) {
                $comment = trim($cm[1]);
                if (in_array($comment, $topMenuNames, true)) {
                    $currentTop = $comment;
                    $currentSub = null;
                } elseif (in_array($comment, $subGroupNames, true)) {
                    $currentSub = $comment;
                }
            }

            // 2. Detect menu-toggle dropdowns: <a ... class="menu-link menu-toggle"> ... <div[^>]*>Name</div>
            if (preg_match('/class="[^"]*menu-toggle[^"]*"/i', $line)) {
                for ($j = $i; $j < min($i + 6, $totalLines); $j++) {
                    if (preg_match('/<div[^>]*>\s*([A-Za-z0-9\s&()–—\.\/-]+)\s*<\/div>/i', $lines[$j], $m)) {
                        $toggleName = trim($m[1]);
                        if (in_array($toggleName, $topMenuNames, true)) {
                            $currentTop = $toggleName;
                            $currentSub = null;
                        } else {
                            $currentSub = $toggleName;
                        }
                        break;
                    }
                }
            }

            // 3. Detect links: href="{{ url('...') }}" or href="{{ route('...') }}"
            if (preg_match('/href="\{\{\s*(url|route)\([\'"]([^\'"]+)[\'"]\)\s*\}\}"/i', $line, $urlMatches)) {
                $rawPath = trim($urlMatches[2]);
                $path = '/' . ltrim($rawPath, '/');

                // Skip profile, logout, root
                if (str_contains($path, 'logout') || str_contains($path, 'profile') || $path === '/') {
                    continue;
                }

                // Find label
                $label = '';
                for ($k = $i; $k < min($i + 6, $totalLines); $k++) {
                    if (preg_match('/<div[^>]*>\s*([A-Za-z0-9\s&()–—\.\/-]+)\s*<\/div>/i', $lines[$k], $lblMatches)) {
                        $label = trim($lblMatches[1]);
                        break;
                    }
                }

                if ($label === '' || $label === 'Dashboard' || $label === 'AI Assistant') {
                    continue;
                }

                // Avoid duplicate chunks for same URL
                if (isset($seenUrls[$path])) {
                    continue;
                }
                $seenUrls[$path] = true;

                // Handle standalone top-level items
                $top = $currentTop;
                if ($path === '/settings') {
                    $top = 'Settings';
                } elseif ($path === '/ticket_management' || $path === '/ticket-management') {
                    $top = 'Ticket Management';
                } elseif ($path === '/taxpro-credentials') {
                    $top = 'TaxPro Credentials';
                }

                $pathParts = [$top];
                if ($currentSub && $currentSub !== $label) {
                    $pathParts[] = $currentSub;
                }
                if ($label !== $top) {
                    $pathParts[] = $label;
                }
                $fullBreadcrumb = implode(' > ', array_unique($pathParts));

                $module = $this->determineModule($top, $label, $path);

                // Dynamically resolve addUrl for this screen
                $addUrl = $this->resolveAddUrl($path, $registeredRouteUris);

                // Build rich search keywords including aliases, synonyms, and Add action terms
                $slug = str_replace(['-', '_', '/'], ' ', $path);
                $cleanLabel = trim(preg_replace('/\s*\([^)]*\)/', '', $label));
                $keywords = [
                    $label,
                    $cleanLabel,
                    str_replace(['(', ')'], '', $label),
                    $slug,
                    $path,
                    ltrim($path, '/'),
                    $top,
                    $currentSub,
                    $module,
                    'screen',
                    'page',
                    'navigation',
                ];

                // Screen-specific synonyms & CRUD Add mappings
                if ($addUrl) {
                    $keywords[] = "add {$label}";
                    $keywords[] = "new {$label}";
                    $keywords[] = "create {$label}";
                    $keywords[] = "how to add {$label}";
                    $keywords[] = "how to create {$label}";
                    $keywords[] = "add new {$label}";
                    $keywords[] = $addUrl;

                    if ($cleanLabel !== $label) {
                        $keywords[] = "add {$cleanLabel}";
                        $keywords[] = "new {$cleanLabel}";
                        $keywords[] = "create {$cleanLabel}";
                        $keywords[] = "how to add {$cleanLabel}";
                    }

                    // Singular variations (e.g. "Credit Notes" -> "add credit note", "Purchase Orders" -> "add purchase order")
                    if (str_ends_with(strtolower($cleanLabel), 's')) {
                        $singular = rtrim($cleanLabel, 'sS');
                        $keywords[] = "add {$singular}";
                        $keywords[] = "new {$singular}";
                        $keywords[] = "create {$singular}";
                        $keywords[] = "how to add {$singular}";
                        $keywords[] = "how to add a {$singular}";
                        $keywords[] = "how to create {$singular}";
                        $keywords[] = "how to create a {$singular}";
                        $keywords[] = "add new {$singular}";
                        $keywords[] = strtolower($singular);
                    }
                }

                // Custom extra aliases for common screens
                if ($path === '/employees') {
                    $keywords = array_merge($keywords, [
                        'employee registration', 'employee creation', 'staff add', '/employees/add'
                    ]);
                } elseif ($path === '/core-material-settings') {
                    $keywords = array_merge($keywords, ['core material settings', 'core planner', 'core material planner settings']);
                } elseif ($path === '/logs') {
                    $keywords = array_merge($keywords, ['audit logs', 'system logs', 'activity log', 'user logs']);
                } elseif ($path === '/job_card_entries') {
                    $keywords = array_merge($keywords, ['jc', 'add jc', 'new jc', 'create jc']);
                } elseif ($path === '/purchase_orders') {
                    $keywords = array_merge($keywords, ['create po', 'add po', 'new po']);
                } elseif ($path === '/sales_orders') {
                    $keywords = array_merge($keywords, ['create so', 'add so', 'new so']);
                }

                $keywordStr = strtolower(implode(', ', array_unique(array_filter($keywords))));

                $navSteps = "In the top navigation bar, click **{$top}** main menu"
                          . ($currentSub && $currentSub !== $label ? " -> **{$currentSub}**" : "")
                          . " -> navigate to **{$label}** (Official Menu Path: `{$fullBreadcrumb}` | URL: `{$path}`)";

                $content = "### Navigation Target: {$label}\n"
                         . "- **Module**: {$module}\n"
                         . "- **Main Menu**: {$top}\n"
                         . ($currentSub && $currentSub !== $label ? "- **Sub Menu**: {$currentSub}\n" : "")
                         . "- **Official Menu Path**: {$fullBreadcrumb}\n"
                         . "- **Direct URL**: `{$path}`\n"
                         . "- **Detailed Menu Navigation Path**: {$navSteps}.\n"
                         . "- **Description**: Official Nachias ERP screen for `{$label}` located under `{$fullBreadcrumb}`.";

                if ($addUrl) {
                    $content .= "\n- **To Add New Record**: In the top navigation bar, click **{$top}** main menu"
                             . ($currentSub && $currentSub !== $label ? " -> **{$currentSub}**" : "")
                             . " -> navigate to **{$label}** (`{$fullBreadcrumb}` | URL: `{$path}`), then click the **Add** button located at the top right of the page, or navigate directly to `{$addUrl}`.";
                }

                $chunks[] = [
                    'source_type' => 'menu',
                    'module' => $module,
                    'screen' => $label,
                    'title' => "Navigation: {$label} ({$fullBreadcrumb})",
                    'keywords' => $keywordStr,
                    'url' => $path,
                    'menu_path' => $fullBreadcrumb,
                    'content' => $content,
                    'meta' => [
                        'top_menu' => $top,
                        'sub_menu' => $currentSub,
                        'label' => $label,
                        'path' => $path,
                        'add_url' => $addUrl,
                        'breadcrumb' => $fullBreadcrumb,
                    ],
                ];
            }
        }
        // Add main Dashboard and AI Assistant
        $chunks[] = [
            'source_type' => 'menu',
            'module' => 'Dashboard',
            'screen' => 'Dashboard',
            'title' => 'Navigation: Dashboard',
            'keywords' => 'dashboard, home, main page, metrics, summary, analytics, /dashboard',
            'url' => '/dashboard',
            'menu_path' => 'Dashboard',
            'content' => "### Navigation Target: Dashboard\n- **Module**: Dashboard\n- **Menu Path**: Dashboard\n- **Direct URL**: `/dashboard`\n- **Description**: Main ERP overview containing Sales & Order Dashboard, Production Dashboard, Fabric & Accessories Inventory drilldowns, and Supplier Performance drilldowns.",
            'meta' => ['path' => '/dashboard'],
        ];

        return $chunks;
    }

    /**
     * Parse registered Laravel routes for controller and action mapping.
     */
    protected function parseRegisteredRoutes(array $menuChunks = []): array
    {
        $chunks = [];
        $routes = Route::getRoutes();

        // Map known menu paths to routes
        $urlToMenu = [];
        foreach ($menuChunks as $m) {
            if (!empty($m['url']) && !empty($m['menu_path'])) {
                $urlToMenu[$m['url']] = $m['menu_path'];
            }
        }

        $seen = [];
        foreach ($routes as $route) {
            $uri = '/' . ltrim($route->uri(), '/');
            $action = $route->getActionName();

            // Skip internal / swagger routes
            if (str_starts_with($uri, '/_') || str_starts_with($uri, '/api') || str_contains($action, 'Closure') || str_contains($action, 'L5Swagger') || $uri === '/') {
                continue;
            }

            if (isset($seen[$uri])) {
                continue;
            }
            $seen[$uri] = true;

            $methods = implode('|', array_diff($route->methods(), ['HEAD']));
            $parts = explode('@', class_basename($action));
            $controller = $parts[0] ?? '';
            $method = $parts[1] ?? '';

            if (empty($controller)) {
                continue;
            }

            $module = $this->guessModuleFromController($controller);

            // Find parent menu path if applicable
            $parentMenu = null;
            foreach ($urlToMenu as $menuUrl => $menuPath) {
                if (str_starts_with($uri, $menuUrl)) {
                    $parentMenu = $menuPath;
                    break;
                }
            }

            $keywords = strtolower(implode(', ', array_unique(array_filter([
                $uri,
                ltrim($uri, '/'),
                str_replace(['-', '_', '/'], ' ', $uri),
                $controller,
                $method,
                $module,
                $parentMenu,
            ]))));

            $content = "### Route Endpoint: {$uri}\n"
                     . "- **Module**: {$module}\n"
                     . (!empty($parentMenu) ? "- **Official Menu Path**: {$parentMenu}\n" : "")
                     . "- **HTTP Method**: {$methods}\n"
                     . "- **URI Path**: `{$uri}`\n"
                     . "- **Controller**: `{$controller}`\n"
                     . "- **Method Action**: `{$method}`\n"
                     . "- **Purpose**: Handles backend processing for `{$uri}` via `{$controller}@{$method}`.";

            $chunks[] = [
                'source_type' => 'route',
                'module' => $module,
                'screen' => $controller,
                'title' => "Route: {$uri} ({$controller}@{$method})",
                'keywords' => $keywords,
                'url' => $uri,
                'menu_path' => $parentMenu,
                'content' => $content,
                'meta' => [
                    'uri' => $uri,
                    'methods' => $methods,
                    'controller' => $controller,
                    'method' => $method,
                    'parent_menu' => $parentMenu,
                ],
            ];
        }

        return $chunks;
    }

    protected function determineModule(string $topMenu, string $label, string $path): string
    {
        $top = strtolower($topMenu);
        $lbl = strtolower($label);
        $p = strtolower($path);

        if ($topMenu === 'Employees' || str_contains($p, 'employee') || str_contains($p, 'role')) {
            return 'Employees';
        }
        if (str_contains($top, 'production') || str_contains($lbl, 'job card') || str_contains($lbl, 'task') || str_contains($p, 'job-card') || str_contains($p, 'task')) {
            return 'Production';
        }
        if (str_contains($top, 'sale') || str_contains($lbl, 'sales') || str_contains($lbl, 'credit note') || str_contains($p, 'sales')) {
            return 'Sales';
        }
        if (str_contains($top, 'purchase') || str_contains($lbl, 'supplier') || str_contains($lbl, 'debit note') || str_contains($lbl, 'grn') || str_contains($p, 'purchase') || str_contains($p, 'grn')) {
            return 'Purchase';
        }
        if (str_contains($top, 'store') || str_contains($top, 'warehouse') || str_contains($lbl, 'stock') || str_contains($lbl, 'raw material') || str_contains($p, 'stock')) {
            return 'Inventory & Store';
        }
        if (str_contains($top, 'payroll') || str_contains($top, 'attendance') || str_contains($lbl, 'attend') || str_contains($lbl, 'salary') || str_contains($lbl, 'payslip') || str_contains($p, 'attend')) {
            return 'Attendance & Payroll';
        }
        if (str_contains($top, 'system utility') || str_contains($p, 'log') || str_contains($p, 'backup') || str_contains($p, 'document_repository')) {
            return 'System Utility';
        }
        if (str_contains($top, 'master') || str_contains($lbl, 'setting')) {
            return 'Master';
        }
        return !empty($topMenu) && $topMenu !== 'javascript:void(0)' ? $topMenu : 'General';
    }

    protected function guessModuleFromController(string $controller): string
    {
        $c = strtolower($controller);
        if (str_contains($c, 'employee') || str_contains($c, 'role')) {
            return 'Employees';
        }
        if (str_contains($c, 'jobcard') || str_contains($c, 'production') || str_contains($c, 'task') || str_contains($c, 'process')) {
            return 'Production';
        }
        if (str_contains($c, 'sales') || str_contains($c, 'creditnote') || str_contains($c, 'retailer')) {
            return 'Sales';
        }
        if (str_contains($c, 'purchase') || str_contains($c, 'debitnote') || str_contains($c, 'grn') || str_contains($c, 'supplier')) {
            return 'Purchase';
        }
        if (str_contains($c, 'stock') || str_contains($c, 'warehouse') || str_contains($c, 'rawmaterial') || str_contains($c, 'item')) {
            return 'Inventory';
        }
        if (str_contains($c, 'attendance') || str_contains($c, 'leave') || str_contains($c, 'shift') || str_contains($c, 'salary')) {
            return 'Attendance & HR';
        }
        if (str_contains($c, 'log') || str_contains($c, 'backup') || str_contains($c, 'documentrepository')) {
            return 'System Utility';
        }
        return 'Master';
    }
}
