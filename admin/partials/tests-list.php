<?php

/**
 * The admin area of the plugin to load the Tests List Table
 */
?>

<div class="wrap">
    <h1 class="wp-heading-inline"><?php _e( 'Tests', $this->plugin_text_domain); ?></h1>
    <a href="<?php echo $this->tests_list_table->get_test_action_link(); ?>" class="page-title-action">Add New</a>
    <div id="vize-wp-list-table">

	    <?php if( !empty($_GET['action']) && $_GET['action'] === 'trash_test'): ?>
		    <?php if ($this->tests_list_table->db_status): ?>
                <div class="notice notice-success is-dismissible">
                    <p><?php _e('Test data has been deleted successfully.', $this->plugin_test_domain) ?></p>
                </div>
		    <?php else: ?>
                <div class="notice notice-error is-dismissible">
                    <p><?php _e('An error has occurred while deleting test. Please try again.', $this->plugin_test_domain) ?></p>
                </div>
		    <?php endif; ?>
	    <?php endif; ?>

        <div id="vize-post-body">
            <form id="vize-test-list-form" method="get">
                <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
				<?php
				$this->tests_list_table->search_box( __( 'Search Test', $this->plugin_text_domain ), 'vize-test-find');
				$this->tests_list_table->display();
				?>
            </form>
        </div>
    </div>
</div>