<?php 
$feedback_url = get_field('feed_back_form_url'); 
?>
<?php if($feedback_url):?>
<div class="editor_note feedback_form mx-5 maxw-720 mx-auto my-5 d-flex flex-column gap-4"> 
    <div class="d-flex gap-1">
        <h4 class="add-motif fw-bold fs-20-24 mb-0"><?php echo esc_html__( 'Enjoyed this article?', 'text-domain' ); ?></h4> 
    </div>

    <p> <?php echo esc_html__(
        'Share your feedback on how you used the article or what you found interesting.',
        'text-domain'
    ); ?></p>

    <a class="btn btn-primary btn-animate gap-2 gap-sm-0" 
       href="<?php echo  esc_url($feedback_url) ; ?>" target='_blank' 
       role="button">
        <span><?php echo esc_html__( 'Reader Feedback Form', 'text-domain' ); ?></span>
        <i class="fas fa-arrow-right ms-2"></i>
    </a>
</div>
<?php endif;?>