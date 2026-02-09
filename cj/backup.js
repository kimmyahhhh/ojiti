$(document).ready(function() {
    
    // File System Access API for Folder Selection
    $('#select-folder').on('click', async () => {
        try {
            if ('showDirectoryPicker' in window) {
                const folderHandle = await window.showDirectoryPicker();
                const folderInfo = document.getElementById('folder-info');
                folderInfo.value = `Selected Folder: ${folderHandle.name}`;
                window.selectedBackupFolder = folderHandle;
            } else {
                alert('File System Access API is not supported in this browser.');
            }
        } catch (error) {
            console.error('Error selecting folder:', error);
        }
    });

    // Handle Form Submission
    $("#backupForm").on('submit', function(e) {
        e.preventDefault();
        
        // UI Updates
        const $btn = $("#start-btn");
        const $progressContainer = $("#progress-container");
        const $progressBar = $("#backup-progress-bar");
        const $progressText = $("#progress-text");
        const originalText = $btn.html();

        $btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin"></i> Initializing...');
        $progressContainer.show();
        $progressBar.css('width', '0%').attr('aria-valuenow', 0);
        $progressText.text('Starting backup...');

        // Start Backup Request
        $.ajax({
            url: '../../routes/inventorymanagement/backup.route.php',
            type: 'POST',
            data: { action: 'StartBackup' },
            dataType: 'JSON',
            success: function(response) {
                if (response.status === 'started') {
                    const backupId = response.backup_id;
                    
                    // Trigger the actual heavy work (fire and forget via AJAX if possible, but PHP blocks sessions usually)
                    // Here we trigger it, and polling will pick up status updates if written to file
                    
                    // Trigger the worker
                    $.ajax({
                        url: '../../routes/inventorymanagement/backup.route.php',
                        type: 'POST',
                        data: { action: 'PerformBackup', backup_id: backupId }
                    });

                    // Start Polling
                    const pollInterval = setInterval(function() {
                        $.ajax({
                            url: '../../routes/inventorymanagement/backup.route.php',
                            type: 'POST',
                            data: { action: 'GetProgress', backup_id: backupId },
                            dataType: 'JSON',
                            success: function(status) {
                                if (status.percent >= 0) {
                                    $progressBar.css('width', status.percent + '%').attr('aria-valuenow', status.percent);
                                    $progressText.text(status.message + ' (' + status.percent + '%)');
                                    
                                    if (status.percent >= 100) {
                                        clearInterval(pollInterval);
                                        $btn.prop('disabled', false).html(originalText);
                                        
                                        Swal.fire({
                                            icon: 'success',
                                            title: 'Backup Complete',
                                            text: 'Database has been backed up and compressed successfully.',
                                            confirmButtonText: 'Download File'
                                        }).then((result) => {
                                            if (result.isConfirmed) {
                                                downloadBackup(status.download_file);
                                            }
                                        });
                                    }
                                } else {
                                    // Error
                                    clearInterval(pollInterval);
                                    $btn.prop('disabled', false).html(originalText);
                                    $progressText.text(status.message);
                                    $progressBar.addClass('bg-danger');
                                    
                                    Swal.fire('Error', status.message, 'error');
                                }
                            },
                            error: function() {
                                // Polling error (network?)
                            }
                        });
                    }, 1000); // Poll every 1 second

                } else {
                    Swal.fire('Error', 'Failed to start backup process', 'error');
                    $btn.prop('disabled', false).html(originalText);
                }
            },
            error: function() {
                Swal.fire('Error', 'Network error starting backup', 'error');
                $btn.prop('disabled', false).html(originalText);
            }
        });
    });

    async function downloadBackup(filename) {
        // Download URL
        const downloadUrl = '../../routes/inventorymanagement/backup.route.php?action=DownloadFile&filename=' + filename;

        if (window.selectedBackupFolder) {
            // Write to selected folder
            try {
                const response = await fetch(downloadUrl);
                const blob = await response.blob();
                const fileHandle = await window.selectedBackupFolder.getFileHandle(filename, { create: true });
                const writable = await fileHandle.createWritable();
                await writable.write(blob);
                await writable.close();
                Swal.fire('Saved', 'File saved to selected folder.', 'success');
            } catch (err) {
                console.error("Save failed", err);
                window.location.href = downloadUrl; // Fallback
            }
        } else {
            // Default browser download
            window.location.href = downloadUrl;
        }
    }
});
