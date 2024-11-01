<?php

/**
 * The admin area of the plugin to add/edit a Test
 */
?>

<div class="wrap">
	<h1>
		<?php
			if (!$this->test_obj):
				_e( 'Add Test', $this->plugin_text_domain);
			else:
				_e( 'Edit Test', $this->plugin_text_domain);
			endif;
		?>
	</h1>
	<p>Create or edit a Test.</p>

    <?php if( !empty($_GET['action']) && $_GET['action'] === 'save_test'): ?>
        <?php if ($this->db_status): ?>
            <div class="notice notice-success is-dismissible">
                <p><?php _e('Information has been saved successfully.', $this->plugin_test_domain) ?></p>
            </div>
        <?php else: ?>
            <div class="notice notice-error is-dismissible">
                <p><?php _e('An error has occurred while saving information. Please try again.', $this->plugin_test_domain) ?></p>
            </div>
        <?php endif; ?>
    <?php endif; ?>

	<form id="vize_test_form" name="vize_test_form" class="vize-tests-forms" method="post"
          action="<?php echo $this->get_test_action_link('save_test', $this->test_obj->ID, false); ?>">

		<table class="form-table" role="presentation">
			<tr class="form-field form-required">
				<th scope="row"><label for="test_name"><?php _e( 'Test Name' ); ?> <span class="required"><?php _e( '*'); ?></span></label></th>
				<td><input name="test_name" type="text" id="test_name" value="<?php _e(esc_attr( $this->test_obj->test_name )); ?>" aria-required="true" autocapitalize="none" autocorrect="off" maxlength="200" /></td>
			</tr>

			<tr class="form-field">
				<th scope="row"><label for="test_description"><?php _e( 'Description' ); ?></label></th>
				<td>
					<textarea name="test_description" id="test_description" autocapitalize="none" autocorrect="off" rows="8" cols="400"><?php _e(esc_attr( $this->test_obj->test_description )); ?></textarea>
				</td>
			</tr>

            <tr class="form-field form-required">
                <th scope="row"><label for="mimimum_score_required"><?php _e( 'Minimum Score (%) Required' ); ?> <span class="required"><?php _e( '*' ); ?></span></label></th>
                <td><input name="mimimum_score_required" type="text" id="mimimum_score_required" value="<?php _e(esc_attr( $this->test_obj->mimimum_score_required )); ?>" aria-required="true" autocapitalize="none" autocorrect="off" maxlength="2" /></td>
            </tr>

		</table>

		<input type="hidden" name="ID" value="<?php _e($this->test_obj->ID); ?>" />
		<?php
		wp_nonce_field( 'save_test_nonce', 'vize_test_nonce' );
		submit_button(__('Save Test', $this->plugin_text_domain));
		?>

	</form>
</div>