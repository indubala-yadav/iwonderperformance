<?php
/**
 * Template Part: Share and PDF Download Buttons
 */
?>

<!-- Share and Download -->

<div class="d-flex align-items-center gap-3 flex-wrap mt-4 mb-5 social-icons-pdf share-pdf-row print-no">
    <?php
    if (function_exists('sharing_display')) {
        echo sharing_display('', true);
    }

    $post_type = get_post_type();
    $files = [];

    // Determine field names based on post type
    if ($post_type === 'resource') {
        $repeater_field = 'resource_upload_files';
        $file_field = 'resource_upload_file';
        $external_url = get_field('external_url');
    } elseif ($post_type === 'magazine_issue') {
        $repeater_field = '_magazine_issues_upload_files';
        $file_field = '_magazine_issues_upload_file';
        $magzine_external_url = get_field('old_magazine_link');
    } elseif ($post_type === 'post') {
        $repeater_field = 'upload_files';
        $file_field = 'upload_file';
        $external_url = get_field('external_url');
    } else {
        $repeater_field = '';
        $file_field = '';
    }

    // Get uploaded files if applicable
    if ($repeater_field && have_rows($repeater_field)) {
        while (have_rows($repeater_field)) {
            the_row();
            $file = get_sub_field($file_field);
            if ($file && isset($file['url'])) {
                $files[] = $file;
            }
        }
        reset_rows();
    }

    // Display PDF download buttons if files exist
    if (!empty($files) && ($post_type !== 'resource' || empty($external_url))) {
        echo '<div id="pdf-downloads" class="d-flex gap-2 flex-wrap">';
        $count = 1;
        foreach ($files as $file) {
            $file_url = esc_url($file['url']);
            $label = count($files) === 1
                ? esc_html(t('iw_download_pdf'))
                : esc_html(t('iw_download_pdf')) . ' ' . $count;

            echo '<a href="' . $file_url . '" class="pdf-btn d-flex align-items-center gap-2 btn btn-light" download="' . basename($file_url) . '" target="_blank">';
            echo '<img src="/wp-content/uploads/2024/04/pdf-svgrepo-com-1.svg" title="' . esc_attr(t('iw_download_pdf')) . '" style="width:auto; height:24px;">';
            echo $label . '</a>';

            $count++;
        }
        echo '</div>';

        if (count($files) > 1) {
            echo '<button id="download-all-btn" class="btn btn-primary"><i class="fas fa-download"></i> ' . esc_html(t('iw_download_all')) . '</button>';
        }
    }

    // Show external URL link if available
    if ($post_type === 'resource' && !empty($external_url)) {
        echo '<a href="' . esc_url($external_url) . '" class="pdf-btn d-flex align-items-center gap-2 btn btn-light" download target="_blank">';
        echo '<img src="/wp-content/uploads/2025/08/pdf-svgrepo-com-1.svg" title="' . esc_attr(t('iw_download_pdf')) . '" style="width:auto; height:24px;">';
        echo esc_html(t('iw_download_pdf')) . '</a>';
    }

    if ($post_type === 'magazine_issue' && !empty($magzine_external_url)) {
        echo '<a href="' . esc_url($magzine_external_url) . '" class="pdf-btn d-flex align-items-center gap-2 btn btn-light" download target="_blank">';
        echo '<img src="/wp-content/uploads/2025/08/pdf-svgrepo-com-1.svg" title="' . esc_attr(t('iw_download_pdf')) . '" style="width:auto; height:24px;">';
        echo '<span>' . esc_html(t('iw_download_pdf')) . '</span></a>';
    }

    // If no files and post type is post only, show generate PDF
    if (empty($files) && $post_type === 'post') {
        echo '<div>';
        error_reporting(E_ALL & ~E_DEPRECATED & ~E_WARNING);
        // echo do_shortcode("[bws_pdfprint display='pdf']");
        echo '</div>';
    }

    if ($post_type === 'post' && !empty($external_url)) {
        echo '<a href="' . esc_url($external_url) . '" class="pdf-btn d-flex align-items-center gap-2 btn btn-light" download target="_blank">';
        echo '<img src="/wp-content/uploads/2025/08/pdf-svgrepo-com-1.svg" title="' . esc_attr(t('iw_download_pdf')) . '" style="width:auto; height:24px;">';
        echo '<span>' . esc_html(t('iw_download_pdf')) . '</span></a>';
    }
    ?>

</div>

<?php if (!empty($files) && count($files) > 1 && ($post_type !== 'resource' || empty($external_url))) : ?>
<script>
document.addEventListener("DOMContentLoaded", function () {
    const downloadAllBtn = document.getElementById("download-all-btn");
    const pdfButtons = document.querySelectorAll(".pdf-btn");

    // for download all files from uploaded files
    if (downloadAllBtn) {
        downloadAllBtn.addEventListener("click", function () {
            pdfButtons.forEach((btn) => {
                const link = document.createElement("a");
                link.href = btn.getAttribute("href");
                link.download = '';
                link.target = "_blank";
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            });
        });
    }

    // for PDF & Print Pro plugin button
    const pdfButton = document.querySelector('.pdfprnt-button-pdf');
    const pdfButtonTitle = document.querySelector('.pdfprnt-button-pdf .pdfprnt-button-title');

    if (pdfButton && pdfButtonTitle) {
        pdfButton.addEventListener('click', function () {
            const originalText = pdfButtonTitle.textContent;
            pdfButtonTitle.textContent = 'Generating PDF, please wait...';

            // Optional: revert the text back after 3 seconds
            setTimeout(() => {
                pdfButtonTitle.textContent = originalText;
            }, 3000);
        });
    }
});
</script>
<?php endif; ?>
