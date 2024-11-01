<?php

/**
 * The admin area of the plugin to load the Questions List Table
 */

$vize_test_id_query_param = '';
if (!empty($_GET['vize_test_id'])) {
   $vize_test_id_query_param = '&vize_test_id=' . absint($_GET['vize_test_id']);
}

?>

<div class="wrap">
    <h1 class="wp-heading-inline"><?php _e( 'Questions', $this->plugin_text_domain); ?></h1>
    <a href="<?php echo $this->test_questions_list_table->get_test_question_action_link() . $vize_test_id_query_param; ?>" class="page-title-action">Add New</a>

    <div id="vize-wp-list-table">

	    <?php if( !empty($_GET['action']) && $_GET['action'] === 'trash_test_question'): ?>
		    <?php if ($this->test_questions_list_table->db_status): ?>
                <div class="notice notice-success is-dismissible">
                    <p><?php _e('Question data has been deleted successfully.', $this->plugin_test_domain) ?></p>
                </div>
		    <?php else: ?>
                <div class="notice notice-error is-dismissible">
                    <p><?php _e('An error has occurred while deleting question. Please try again.', $this->plugin_test_domain) ?></p>
                </div>
		    <?php endif; ?>
	    <?php endif; ?>

        <div id="vize-post-body">
            <form id="vize-test-question-list-form" method="get">
                <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
				<?php
				$this->test_questions_list_table->search_box( __( 'Search Question', $this->plugin_text_domain ), 'vize-test-question-find');
				$this->test_questions_list_table->display();
				?>
            </form>
        </div>
    </div>
</div>