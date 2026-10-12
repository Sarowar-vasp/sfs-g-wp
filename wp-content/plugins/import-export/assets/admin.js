jQuery(document).ready(function($) {
    
    $('#wpmb-export-btn').on('click', function(e) {
        e.preventDefault();
        
        const btn = $(this);
        const postCount = $('#post-count').val();
        const postOffset = $('#post-offset').val();
        const includeMeta = $('#include-meta').is(':checked');
        const loader = $('#wpmb-loader');
        const message = $('#export-message');
        
        btn.prop('disabled', true);
        loader.removeClass('wpmb-hidden').css('display', 'flex');
        message.removeClass('success error').hide();
        
        $.ajax({
            url: wpmb_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'wpmb_export',
                nonce: wpmb_ajax.nonce,
                post_count: postCount,
                post_offset: postOffset,
                include_meta: includeMeta
            },
            success: function(response) {
                loader.addClass('wpmb-hidden').hide();
                btn.prop('disabled', false);
                
                if (response.success) {
                    message.addClass('success').text('Export bundle generated successfully! Downloading...').show();
                    
                    // Trigger download
                    const link = document.createElement('a');
                    link.href = response.data.download_url;
                    link.download = response.data.filename;
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                } else {
                    message.addClass('error').text('Error: ' + response.data).show();
                }
            },
            error: function() {
                loader.addClass('wpmb-hidden').hide();
                btn.prop('disabled', false);
                message.addClass('error').text('An unexpected error occurred.').show();
            }
        });
    });

    // Success/Error messages from redirect
    // Local Import with Progress Bar
    $('#wpmb-local-import-btn').on('click', function(e) {
        e.preventDefault();
        
        const btn = $(this);
        const progressContainer = $('#local-import-progress');
        const progressBar = progressContainer.find('.progress-bar');
        const statusText = progressContainer.find('.status-text');
        const statusPercent = progressContainer.find('.status-percent');

        btn.prop('disabled', true).text('Initializing...');
        progressContainer.show();

        // 1. Get Count
        $.post(wpmb_ajax.ajax_url, {
            action: 'wpmb_get_import_count',
            nonce: wpmb_ajax.nonce
        }, function(response) {
            if (response.success) {
                const total = response.data.count;
                const sessionId = response.data.session_id;
                let current = 0;

                statusText.text('Found ' + total + ' posts. Starting import...');

                // 2. Process recursively
                function processNext() {
                    if (current < total) {
                        $.post(wpmb_ajax.ajax_url, {
                            action: 'wpmb_process_import_item',
                            nonce: wpmb_ajax.nonce,
                            session_id: sessionId,
                            index: current
                        }, function(res) {
                            current++;
                            const percent = Math.round((current / total) * 100);
                            progressBar.css('width', percent + '%');
                            statusPercent.text(percent + '%');
                            statusText.text('Importing post ' + current + ' of ' + total + '...');
                            
                            processNext();
                        });
                    } else {
                        statusText.text('Import complete! ' + total + ' posts processed.');
                        btn.text('Import Complete').addClass('success');
                        setTimeout(() => location.reload(), 2000);
                    }
                }

                processNext();
            } else {
                alert('Error: ' + response.data);
                btn.prop('disabled', false).text('Start Smart Import');
            }
        });
    });
});
