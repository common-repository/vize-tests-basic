<?php

/**
 * The admin area of the plugin to add/edit a Test Question
 */
?>

<div class="wrap">
	<h1 class="wp-heading-inline">
		<?php
			if (!$this->test_question_obj):
				_e( 'Add Test Question & Answers', $this->plugin_text_domain);
			else:
				_e( 'Edit Test Question & Answers', $this->plugin_text_domain);
			endif;
		?>
	</h1>

    <?php if (isset($this->test_question_obj) && isset($this->test_question_obj->vize_test_id)): ?>
    <a href="<?php echo $this->get_test_question_action_link() . '&vize_test_id=' . $this->test_question_obj->vize_test_id; ?>" class="page-title-action">Add New</a>
    <?php endif; ?>

	<p>Create or edit a Test Question.</p>

    <?php if( !empty($_GET['action']) && $_GET['action'] === 'save_test_question'): ?>
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

	<form id="vize_test_question_form" name="vize_test_question_form" class="vize-tests-forms" method="post"
          action="<?php echo $this->get_test_question_action_link('save_test_question', $this->test_question_obj->ID, false); ?>"
          enctype="multipart/form-data">

		<table class="form-table" role="presentation">

			<tr class="form-field form-required">
				<th scope="row"><label for="question_text"><?php _e( 'Question', $this->plugin_text_domain ); ?> <span class="description"><?php _e( '(required)' ); ?></span></label></th>
				<td>
                    <textarea name="question_text" id="question_text" aria-required="true" autocapitalize="none" autocorrect="off" style="height: 5em;" rows="3" cols="200"><?php _e(esc_attr( $this->test_question_obj->question_text )); ?></textarea>
                </td>
			</tr>

            <tr class="form-field form-required">
                <th scope="row"><label for="vize_test_id"><?php _e( 'Test', $this->plugin_text_domain ); ?></label></th>
                <td>
                    <select id="vize_test_id" name="vize_test_id">
                        <option value="">Please Select</option>
                        <?php
                            $vize_tests = VIZE_Test::get_all_tests();
                            if (isset($vize_tests) && sizeof($vize_tests) > 0):
                                foreach ($vize_tests as $item):

	                                $selected = '';

	                                if( $_GET['vize_test_id'] == $item['ID'] ){
		                                $selected = ' selected = "selected"';
	                                }

	                                if( $this->test_question_obj->vize_test_id == $item['ID'] ){
		                                $selected = ' selected = "selected"';
	                                }
                        ?>
                                <option value="<?php _e($item['ID']); ?>" <?php _e($selected); ?>><?php _e($item['test_name']); ?></option>
                        <?php
                                endforeach;
                            endif;
                        ?>
                    </select>
                </td>
            </tr>

            <tr class="form-field form-required">
                <th scope="row"><label for="sorting_order"><?php _e( 'Sort Order', $this->plugin_text_domain ); ?> <span class="description"><?php _e( '(required)' ); ?></span></label></th>
                <td>
                    <input type="text" name="sorting_order" id="sorting_order" aria-required="true" autocapitalize="none" autocorrect="off" value="<?php _e(esc_attr( $this->test_question_obj->sorting_order )); ?>" />
                </td>
            </tr>

            <tr class="form-field form-required">
                <td scope="row" colspan="2" style="padding: 0px !important;">
                    <fieldset class="answers-container">
                        <legend><?php _e( 'Answers', $this->plugin_text_domain); ?> <span class="description"><?php _e( '(required)' ); ?></span></legend>
                        <table id="question_answers_container">
                            <tr>
                                <th style="padding-left: 10px;"><?php _e( 'Answer', $this->plugin_text_domain); ?></th>
                                <th style="width: 140px; white-space: nowrap;"><?php _e( 'Mark Correct Option', $this->plugin_text_domain); ?></th>
                                <th></th>
                            </tr>

                            <?php $add_default_rows = true; ?>

                            <?php
                                if (!empty($this->test_question_obj->ID)):
                                    $vize_test_answers =
                                        VIZE_Test_Answer::get_all_test_answers_by_question_id($this->test_question_obj->ID);

                                    //var_dump($vize_test_answers);

                                    if (isset($vize_test_answers) && sizeof($vize_test_answers) > 0):
                                        $add_default_rows = false;

                                        foreach ($vize_test_answers as $item):
                            ?>
                                            <tr>
                                                <td>
                                                    <textarea id="answer_text_<?php _e($item['ID']); ?>"
                                                              name="answer_text[]" class="answer_text" aria-required="true"
                                                              autocapitalize="none" autocorrect="off"
                                                              style="height: 4em;"
                                                              rows="3" cols="200"><?php _e($item['answer_text']); ?></textarea>
                                                </td>
                                                <td style="text-align: center;">

                                                    <?php
                                                        $is_checked = '';
                                                        if ($item['is_correct_answer'] == '1'){
	                                                        $is_checked = ' checked="checked"';
                                                        }
                                                    ?>

                                                    <label class="input-radio input-block">
                                                        <input type="radio" name="is_correct_answer" class="correct_answer" <?php _e($is_checked); ?> />
                                                        <span class="checkmark"></span>
                                                    </label>

                                                </td>
                                            </tr>
                            <?php
                                            endforeach;
                                    endif;
                                endif;
                            ?>

                            <?php if ($add_default_rows): ?>
                            <tr>
                                <td>
                                    <textarea name="answer_text[]" id="answer_text_1" class="answer_text" aria-required="true" autocapitalize="none" autocorrect="off" style="height: 4em;" rows="3" cols="200"></textarea>
                                </td>
                                <td style="text-align: center;">

                                    <label class="input-radio input-block">
                                        <input type="radio" name="is_correct_answer" class="correct_answer" />
                                        <span class="checkmark"></span>
                                    </label>

                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <textarea name="answer_text[]" id="answer_text_2" class="answer_text" aria-required="true" autocapitalize="none" autocorrect="off" style="height: 4em;" rows="3" cols="200"></textarea>
                                </td>
                                <td style="text-align: center;">

                                    <label class="input-radio input-block">
                                        <input type="radio" name="is_correct_answer" class="correct_answer" />
                                        <span class="checkmark"></span>
                                    </label>

                                </td>
                            </tr>
                            <?php endif; ?>

                            <?php
                            /*
                            <td>
                                <input type="button" class="button button-small button-cancel" value="X" />
                            </td>
                            */
                            ?>

                        </table>
                        <div style="padding-left: 15px;">
                            <input type="button" id="add_answer_button" value="Add Answer" class="button button-link" />

                        </div>
                    </fieldset>
                </td>
            </tr>

		</table>

		<input type="hidden" name="ID" id="test_question_id" value="<?php _e($this->test_question_obj->ID); ?>" />
        <p>
            <?php wp_nonce_field( 'save_test_question_nonce', 'vize_test_nonce' ); ?>
            <?php //submit_button(__('Save Question', $this->plugin_text_domain)); ?>

            <?php echo (get_submit_button(__('Save Question', $this->plugin_text_domain), 'primary', 'submit', false)); ?>

            <?php
            $back_button_text = 'Back';
            $back_href = $this->get_manage_questions_link();

            if (isset($_REQUEST['vize_test_id'])) {
	            $back_href = $this->get_manage_questions_link($_REQUEST['vize_test_id']);
            } else if (isset($this->test_question_obj->vize_test_id)) {
                $back_button_text = 'Back to List';
	            $back_href = $this->get_manage_questions_link($this->test_question_obj->vize_test_id);
            }

            ?>

            <a href="<?php echo $back_href; ?>" class="button"><?php _e($back_button_text); ?></a>
        </p>

	</form>
</div>