<?php

/**
 * VIZE Test Body View
 * It will be used to render VIZE Test in a page or post using one of the short codes.
 *
 * @since      1.0.0
 *
 * @package    VIZE_Tests
 * @subpackage VIZE_Tests/public/partials
 */
?>
<div id="vize_tests_container" class="vize_tests_wrapper">

<?php
    if ( isset($this->vize_test_data['message']) ){
        $message_class = (isset($this->vize_test_data['type'])
                            && $this->vize_test_data['type'] == 'error')? 'alert-danger' : 'alert-success';
?>
    <div class="alert <?php _e($message_class); ?> alert-dismissible">
        <?php _e($this->vize_test_data['message'], $this->plugin_text_domain); ?>
    </div>
<?php
        // Return if we have a message and no data!
        return;
    }

    if (isset($this->vize_test_data['data']) && sizeof($this->vize_test_data['data']) > 0){
?>
        <form id="vize_test_form" name="vize_test_form" action="<?php _e(get_permalink()); ?>">

            <input type="hidden" id="vize_test_id" name="vize_test_id"
                   value="<?php _e($this->vize_test_data['data']['ID']); ?>" />

            <?php /*Start - Render Question Logic*/ ?>
            <?php
            if (isset($this->vize_test_data['data']['questions']) && sizeof($this->vize_test_data['data']['questions']) > 0) {
                $questions = $this->vize_test_data['data']['questions'];
            ?>
                <input type="hidden" name="questions_count" value="<?php _e(sizeof($questions)); ?>" />

                <?php for ($i = 0; $i < sizeof($questions); $i++){ ?>

                    <div class="vize_test_question_container">

                        <p class="vize_test_question">
                            <?php _e($i+1); ?>. <?php _e($questions[$i]['question_text']); ?>
                        </p>

                        <div class="vize_test_answers_wrapper">
                            <?php
                            if (isset($questions[$i]['answers']) && sizeof($questions[$i]['answers']) > 0) {
                                $answers = $questions[$i]['answers'];
                            ?>
                            <table cellpadding="0" cellspacing="0" border="0">

                                    <?php for($j = 0; $j < sizeof($answers); $j++) { ?>

                                        <tr>
                                            <td class="answer_text">
                                                <label for="vize_test_answer_option_<?php _e($answers[$j]['ID']); ?>">
                                                    <?php _e($answers[$j]['answer_text']); ?>
                                                </label>
                                            </td>

                                            <td style="vertical-align: middle; width:30px;">
                                                <span class="answer_radio_container">

                                                    <?php
                                                        $checked = '';
                                                        if ( isset($questions[$i]['vize_answer_id'])
                                                             && $questions[$i]['vize_answer_id'] === $answers[$j]['ID'] ) {
                                                            $checked = 'checked="checked"';
                                                        }

                                                        $disabled = '';
                                                        $label_class = 'checkmark';
                                                        if (isset($this->view_mode) && $this->view_mode === "review") {

                                                            $disabled = 'disabled="disabled"';
                                                            $show_explanation = true;

                                                            if(isset($answers[$j]['is_correct_answer'])
                                                                    && $answers[$j]['is_correct_answer'] == '1'
                                                                    && isset($questions[$i]['vize_answer_id'])
                                                                    && $answers[$j]['ID'] != $questions[$i]['vize_answer_id']) {
                                                                $label_class = 'correct_answer';
                                                                //$show_explanation = true;
                                                            }
                                                        }
                                                    ?>

                                                    <input type="radio" class="vize_test_answer_option required"
                                                           id="vize_test_answer_option_<?php _e($answers[$j]['ID']); ?>"
                                                           name="vize_test_question_<?php _e($i); ?>"
                                                           <?php _e($checked); ?>
                                                           <?php _e($disabled); ?>
                                                           value="<?php _e($answers[$j]['vize_test_question_id']); ?>_<?php _e($answers[$j]['ID']); ?>" />

                                                    <label for="vize_test_answer_option_<?php _e($answers[$j]['ID']); ?>" class="<?php _e($label_class); ?>"></label>

                                                </span>
                                            </td>
                                        </tr>

                                    <?php } ?>

                                </table>
                            <?php
                            } // End Answers If
                            ?>

                        </div> <!-- .vize_test_answers_wrapper -->

                        <div class="clear"></div>

                    </div> <!-- .vize_test_question_container -->
                    
                <?php } // End Questions For ?>

                <div id="ajax_alert" class="alert alert-dismissible" style="display: none"></div>

                <div class="vize_tests_results_loading_wrapper" style="display: none;">
                    <div class="loading-text">Loading Results ...</div>
                    <div class="lds-dual-ring"></div>
                </div>

                <div class="vize_tests_results_wrapper" style="display: none;">

                    <div class="test_score_indicator">
                        <div class="user_score_value">
                            SCORE:
                        </div>
                        <div class="user_score_indicator">
                            &nbsp;
                        </div>
                    </div>


                    <h2 class="test_result"></h2>

                </div>

                <div class="vize_tests_navigation_wrapper">

                    <button type="submit" id="vize_test_submit_button" class="vize_test_navigation_button">
                        Get Result
                    </button>

                    <button type="button" id="rewrite_test_button" class="vize_test_navigation_button" style="display: none;">
                        Rewrite Test
                    </button>

                </div>
                
            <?php } // End Questions If ?>
            
            <?php /*End - Render Question Logic*/ ?>
        </form>
<?php
    }
?>
</div> <!-- .vize_tests_wrapper -->


