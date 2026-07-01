<?php

/**
 * Domain Refactoring Script
 * 
 * Usage: php scripts/refactor-domains.php
 * 
 * Moves flat app/ structure into app/Domain/{Module}/ structure.
 * Old namespace: App\Models\User  →  New: App\Domain\Auth\Models\User
 * Old namespace: App\Services\UserService  →  New: App\Domain\Auth\Services\UserService
 * etc.
 * 
 * The script:
 *   1. Creates domain directories
 *   2. Copies files to new locations with updated namespaces
 *   3. Updates all use statements across the codebase
 *   4. Updates bootstrap/providers.php and routes/api.php
 *   5. Removes old files (after confirmation)
 */

declare(strict_types=1);

$root = dirname(__DIR__);
chdir($root);

// ─── Module Map: entity → module ────────────────────────────────────────────
$entityModuleMap = [
    // Auth
    'Role' => 'Auth',
    'User' => 'Auth',
    // AuthController is special (cross-cutting)
    
    // Academic
    'AcademicYear' => 'Academic',
    'Term' => 'Academic',
    'ClassLevel' => 'Academic',
    'Stream' => 'Academic',
    
    // Staff
    'Staff' => 'Staff',
    
    // Students
    'Student' => 'Students',
    'Guardian' => 'Students',
    'StudentGuardian' => 'Students',
    'Enrollment' => 'Students',
    
    // Curriculum
    'Subject' => 'Curriculum',
    'ClassSubject' => 'Curriculum',
    'SubjectTeacher' => 'Curriculum',
    'CurriculumTheme' => 'Curriculum',
    'LearningOutcome' => 'Curriculum',
    'GenericSkill' => 'Curriculum',
    
    // Assessment
    'AssessmentType' => 'Assessment',
    'GradingScale' => 'Assessment',
    'SkillRatingScale' => 'Assessment',
    'AssessmentRecord' => 'Assessment',
    'GenericSkillRating' => 'Assessment',
    'SubjectTermResult' => 'Assessment',
    
    // Reports
    'ReportCard' => 'Reports',
    
    // Attendance
    'Attendance' => 'Attendance',
    
    // Timetable
    'Timetable' => 'Timetable',
    
    // Finance
    'FeeStructure' => 'Finance',
    'Invoice' => 'Finance',
    'Payment' => 'Finance',
    
    // Discipline
    'DisciplineRecord' => 'Discipline',
    
    // Library
    'LibraryBook' => 'Library',
    'BookLoan' => 'Library',
    
    // Communication
    'Announcement' => 'Announcements',
];

// Extra controllers that don't map to a single entity
$extraControllerModuleMap = [
    'AuthController' => 'Auth',
];

$modules = array_unique(array_values($entityModuleMap));
sort($modules);

// ─── Step 1: Create directories ────────────────────────────────────────────
echo "=== Creating domain directories ===\n";
foreach ($modules as $module) {
    $dirs = [
        "app/Domain/{$module}",
        "app/Domain/{$module}/Models",
        "app/Domain/{$module}/Controllers",
        "app/Domain/{$module}/Services",
        "app/Domain/{$module}/Services/Contracts",
        "app/Domain/{$module}/Repositories",
        "app/Domain/{$module}/Repositories/Contracts",
        "app/Domain/{$module}/Repositories/Eloquent",
        "app/Domain/{$module}/Requests",
        "app/Domain/{$module}/Resources",
        "app/Domain/{$module}/Providers",
        "app/Domain/{$module}/routes",
    ];
    foreach ($dirs as $dir) {
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
            echo "  Created: {$dir}\n";
        }
    }
}

// ─── Step 2: Map old files to new locations ─────────────────────────────────
echo "\n=== Mapping files ===\n";

$moves = [];

foreach ($entityModuleMap as $entity => $module) {
    $oldNamespace = 'App\\';
    $newNamespace = "App\\Domain\\{$module}\\";
    
    $files = [
        // Model
        "app/Models/{$entity}.php" => "app/Domain/{$module}/Models/{$entity}.php",
        // Controller
        "app/Http/Controllers/Api/{$entity}Controller.php" => "app/Domain/{$module}/Controllers/{$entity}Controller.php",
        // Service
        "app/Services/{$entity}Service.php" => "app/Domain/{$module}/Services/{$entity}Service.php",
        // Service Interface
        "app/Services/Contracts/{$entity}ServiceInterface.php" => "app/Domain/{$module}/Services/Contracts/{$entity}ServiceInterface.php",
        // Repository
        "app/Repositories/Eloquent/{$entity}Repository.php" => "app/Domain/{$module}/Repositories/Eloquent/{$entity}Repository.php",
        // Repository Interface
        "app/Repositories/Contracts/{$entity}RepositoryInterface.php" => "app/Domain/{$module}/Repositories/Contracts/{$entity}RepositoryInterface.php",
        // Request
        "app/Http/Requests/{$entity}Request.php" => "app/Domain/{$module}/Requests/{$entity}Request.php",
        // Resource
        "app/Http/Resources/{$entity}Resource.php" => "app/Domain/{$module}/Resources/{$entity}Resource.php",
        // Collection
        "app/Http/Resources/{$entity}Collection.php" => "app/Domain/{$module}/Resources/{$entity}Collection.php",
        // Provider
        "app/Providers/{$entity}ServiceProvider.php" => "app/Domain/{$module}/Providers/{$entity}ServiceProvider.php",
    ];
    
    foreach ($files as $old => $new) {
        if (file_exists($old)) {
            $moves[] = [$old, $new, $oldNamespace, $newNamespace, $entity, $module];
        }
    }
}

// Add extra files
$extraMoves = [
    ["app/Http/Controllers/Api/AuthController.php", "app/Domain/Auth/Controllers/AuthController.php", 'App\\Http\\Controllers\\Api\\', 'App\\Domain\\Auth\\Controllers\\'],
    ["app/Providers/RepositoryServiceProvider.php", "app/Domain/Shared/Providers/RepositoryServiceProvider.php", 'App\\Providers\\', 'App\\Domain\\Shared\\Providers\\'],
    // routes
    ["routes/api_v1", "app/Domain/routes", '', ''],
];

// Copy files
echo "\n=== Copying files with namespace updates ===\n";

$changedFiles = [];

foreach ($moves as [$old, $new, $oldNs, $newNs, $entity, $module]) {
    if (!file_exists($old)) {
        continue;
    }
    $content = file_get_contents($old);
    
    // Derive old namespace from old file path
    // app/Http/Controllers/Api/AcademicYearController.php → App\Http\Controllers\Api
    $oldRelPath = str_replace('app/', '', $old);
    $oldRelPath = preg_replace('/\.php$/', '', $oldRelPath);
    $oldNsFromPath = 'App\\' . str_replace('/', '\\', dirname($oldRelPath));
    
    // Derive new namespace from new file path
    // app/Domain/Academic/Controllers/AcademicYearController.php → App\Domain\Academic\Controllers
    $newRelPath = str_replace('app/', '', $new);
    $newRelPath = preg_replace('/\.php$/', '', $newRelPath);
    $newNsFromPath = 'App\\' . str_replace('/', '\\', dirname($newRelPath));
    
    $content = str_replace(
        "namespace {$oldNsFromPath}",
        "namespace {$newNsFromPath}",
        $content
    );
    
    file_put_contents($new, $content);
    echo "  {$old}\n    → {$new}\n";
    $changedFiles[] = $new;
}

// Copy route files
echo "\n=== Copying route files ===\n";
$routeDir = 'routes/api_v1';
if (is_dir($routeDir)) {
    $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($routeDir, RecursiveDirectoryIterator::SKIP_DOTS));
    foreach ($it as $file) {
        if ($file->isFile() && $file->getExtension() === 'php') {
            $relPath = str_replace('\\', '/', $it->getSubPathname());
            // Keep routes in their domain folders
            // Extract module from path
            $parts = explode('/', $relPath);
            $routeModule = $parts[0] ?? '';
            // Map route module name to domain module
            $routeModuleMap = [
                'auth' => 'Auth', 'users' => 'Auth', 'roles' => 'Auth',
                'academic_years' => 'Academic', 'terms' => 'Academic', 'class_levels' => 'Academic', 'streams' => 'Academic',
                'staff' => 'Staff',
                'students' => 'Students', 'guardians' => 'Students', 'enrollments' => 'Students',
                'subjects' => 'Curriculum', 'class_subjects' => 'Curriculum', 'subject_teachers' => 'Curriculum',
                'curriculum_themes' => 'Curriculum', 'learning_outcomes' => 'Curriculum', 'generic_skills' => 'Curriculum',
                'assessment_types' => 'Assessment', 'grading_scale' => 'Assessment', 'skill_rating_scale' => 'Assessment',
                'assessment_records' => 'Assessment', 'generic_skill_ratings' => 'Assessment', 'subject_term_results' => 'Assessment',
                'report_cards' => 'Reports',
                'attendance' => 'Attendance',
                'timetable' => 'Timetable',
                'fee_structures' => 'Finance', 'invoices' => 'Finance', 'payments' => 'Finance',
                'discipline_records' => 'Discipline',
                'library_books' => 'Library', 'book_loans' => 'Library',
                'announcements' => 'Announcements',
            ];
            $domain = $routeModuleMap[$routeModule] ?? 'Shared';
            $destDir = "app/Domain/{$domain}/routes/{$routeModule}";
            if (!is_dir($destDir)) {
                mkdir($destDir, 0755, true);
            }
            $dest = "{$destDir}/{$file->getFilename()}";
            copy($file->getPathname(), $dest);
            echo "  {$file->getPathname()}\n    → {$dest}\n";
        }
    }
}

exit(0); // Phase 1 done. Run scripts/update-namespaces.php for use statement updates.

// ─── Step 3: Update use statements in all PHP files ─────────────────────────
echo "\n=== Updating use statements across codebase ===\n";

$oldToNewNamespace = [];
foreach ($entityModuleMap as $entity => $module) {
    $oldToNewNamespace["App\\Models\\{$entity}"] = "App\\Domain\\{$module}\\Models\\{$entity}";
    $oldToNewNamespace["App\\Http\\Controllers\\Api\\{$entity}Controller"] = "App\\Domain\\{$module}\\Controllers\\{$entity}Controller";
    $oldToNewNamespace["App\\Services\\{$entity}Service"] = "App\\Domain\\{$module}\\Services\\{$entity}Service";
    $oldToNewNamespace["App\\Services\\Contracts\\{$entity}ServiceInterface"] = "App\\Domain\\{$module}\\Services\\Contracts\\{$entity}ServiceInterface";
    $oldToNewNamespace["App\\Repositories\\Eloquent\\{$entity}Repository"] = "App\\Domain\\{$module}\\Repositories\\Eloquent\\{$entity}Repository";
    $oldToNewNamespace["App\\Repositories\\Contracts\\{$entity}RepositoryInterface"] = "App\\Domain\\{$module}\\Repositories\\Contracts\\{$entity}RepositoryInterface";
    $oldToNewNamespace["App\\Http\\Requests\\{$entity}Request"] = "App\\Domain\\{$module}\\Requests\\{$entity}Request";
    $oldToNewNamespace["App\\Http\\Resources\\{$entity}Resource"] = "App\\Domain\\{$module}\\Resources\\{$entity}Resource";
    $oldToNewNamespace["App\\Http\\Resources\\{$entity}Collection"] = "App\\Domain\\{$module}\\Resources\\{$entity}Collection";
    $oldToNewNamespace["App\\Providers\\{$entity}ServiceProvider"] = "App\\Domain\\{$module}\\Providers\\{$entity}ServiceProvider";
}
$oldToNewNamespace["App\\Http\\Controllers\\Api\\AuthController"] = "App\\Domain\\Auth\\Controllers\\AuthController";
$oldToNewNamespace["App\\Providers\\RepositoryServiceProvider"] = "App\\Domain\\Shared\\Providers\\RepositoryServiceProvider";

// Collect all PHP files in the project (excluding vendor)
$allPhpFiles = [];
$dirsToScan = ['app', 'config', 'database', 'routes', 'tests'];
foreach ($dirsToScan as $dir) {
    if (!is_dir($dir)) continue;
    $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
    foreach ($it as $file) {
        if ($file->isFile() && $file->getExtension() === 'php') {
            $allPhpFiles[] = $file->getPathname();
        }
    }
}

$replaceCount = 0;
foreach ($allPhpFiles as $file) {
    if (!is_file($file)) continue;
    $content = file_get_contents($file);
    if ($content === false) continue;
    $original = $content;
    
    foreach ($oldToNewNamespace as $oldNs => $newNs) {
        // Replace use statements
        $content = str_replace(
            "use {$oldNs};",
            "use {$newNs};",
            $content
        );
        // Replace inline references (e.g., in PHPDoc or strings)
        // Only replace when preceded by non-alphanumeric
        $content = preg_replace(
            "/(?<=[^\\\\a-zA-Z0-9]){$oldNs}(?=[^a-zA-Z0-9])/",
            $newNs,
            $content
        );
    }
    
    if ($content !== $original) {
        file_put_contents($file, $content);
        $replaceCount++;
        echo "  Updated: {$file}\n";
    }
}

echo "\n  Files with use statement updates: {$replaceCount}\n";

// ─── Step 4: Update bootstrap/providers.php ─────────────────────────────────
echo "\n=== Updating bootstrap/providers.php ===\n";
$providersFile = 'bootstrap/providers.php';
$content = file_get_contents($providersFile);
foreach ($oldToNewNamespace as $oldNs => $newNs) {
    $content = str_replace($oldNs, $newNs, $content);
}
file_put_contents($providersFile, $content);
echo "  Updated: {$providersFile}\n";

// ─── Step 5: Update routes/api.php ──────────────────────────────────────────
echo "\n=== Updating routes/api.php ===\n";
$routesFile = 'routes/api.php';
$content = file_get_contents($routesFile);
$content = str_replace(
    "require __DIR__.'/api_v1/",
    "require __DIR__.'/../app/Domain/routes/",
    $content
);
file_put_contents($routesFile, $content);
echo "  Updated: {$routesFile}\n";

// ─── Step 6: Update route files themselves ──────────────────────────────────
echo "\n=== Updating route file controller references ===\n";
$domainRoutesDir = 'app/Domain';
$it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($domainRoutesDir));
foreach ($it as $file) {
    if ($file->isFile() && $file->getExtension() === 'php') {
        $content = file_get_contents($file->getPathname());
        $original = $content;
        
        foreach ($oldToNewNamespace as $oldNs => $newNs) {
            if (str_contains($oldNs, 'Controller')) {
                $content = str_replace(
                    "use {$oldNs};",
                    "use {$newNs};",
                    $content
                );
            }
        }
        
        if ($content !== $original) {
            file_put_contents($file->getPathname(), $content);
            echo "  Updated: {$file->getPathname()}\n";
        }
    }
}

// ─── Step 7: Dump autoload ──────────────────────────────────────────────────
echo "\n=== Running composer dump-autoload ===\n";
passthru('composer dump-autoload 2>&1', $exitCode);
echo $exitCode === 0 ? "  Done\n" : "  WARNING: composer dump-autoload may have failed\n";

// ─── Summary ────────────────────────────────────────────────────────────────
echo "\n========================================\n";
echo "  Refactoring complete!\n";
echo "========================================\n";
echo "\n";
echo "  What happened:\n";
echo "  - Created " . count($modules) . " domain folders under app/Domain/\n";
echo "  - Moved " . count($moves) . " files with updated namespaces\n";
echo "  - Updated use statements in {$replaceCount} files\n";
echo "  - Updated bootstrap/providers.php\n";
echo "  - Updated routes/api.php\n";
echo "\n";
echo "  Next steps:\n";
echo "  1. Run: php artisan test\n";
echo "  2. If tests pass, remove old files:\n";
echo "     rm -rf app/Models app/Http/Controllers/Api app/Services\n";
echo "     rm -rf app/Repositories app/Http/Requests app/Http/Resources\n";
echo "     rm -rf app/Providers app/Repositories\n";
echo "     rm -rf routes/api_v1\n";
echo "  3. Run tests again\n";
echo "========================================\n";
