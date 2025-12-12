<?php
require_once 'system/bootstrap.php';

use App\Models\AnnouncementModel;

echo "=== TESTING ANNOUNCEMENT ACCESS ===\n\n";

try {
    $model = new AnnouncementModel();

    // Test getting announcements for 'all' audience
    echo "Getting announcements for 'all' audience:\n";
    $announcements = $model->getActiveAnnouncementsFor('all', 20);

    if (empty($announcements)) {
        echo "No announcements found\n";
    } else {
        echo "Found " . count($announcements) . " announcements:\n";
        foreach ($announcements as $ann) {
            echo "- " . $ann['title'] . " (ID: " . $ann['id'] . ")\n";
        }
    }

    echo "\nTest completed successfully\n";

} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . " Line: " . $e->getLine() . "\n";
}
?>
