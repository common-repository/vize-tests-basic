=== VIZE Tests - Basic ===
Contributors: zeeshanelahi83
Tags: Test, Quiz, MCQs, Multiple Choice Questions
Requires at least: 5.0
Tested up to: 5.7
Requires PHP: 7.0
Stable tag: 1.0.0
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl-3.0.html

== Description ==

This plugin will help you to create and configure different type of tests with multiple choice questions. And embed those tests in any Post or Page using Shortcode.

Plugin will instantly analyze user answers and display result as "You Passed" or "You Failed" along with percentage score of a user.

User can rewrite or reattempt any test at any number of time.

== Shortcode Or Settings ==
- [VIZE_Test_Body]
- vize_test_id - Only required argument to embed a Test in a Post or Page.

== How to Integrate ==
- First create one or more tests using "VIZE Tests -> Manage Tests" WordPress menu item.
- Now add one or more questions to one or more tests.
- A question can only be linked to one test at one time.
- A question can have two or more answers in it.
- You can add more answers to any question using "Add Answer" link on Add/Edit Question form.
- Now go to "VIZE Tests -> Manage Tests" page and copy shortcode of a test from "Test ID / Shortcode" column.
- Shortcode format is [VIZE_Test_Body vize_test_id="xx"].
- Replace "xx" with ID of Test which you want to embed in your post.
- For example shortcode for Test with ID "1" will look like this. [VIZE_Test_Body vize_test_id="1"]

== Style and Layout ==
- Most of the layout and styling has been managed by using Classes and IDs.
- All Frontend styles and layout has been tested using WordPress TwentyTwenty theme.
- You can override any style in your theme style file by using class name or ID (Recommended).
- Otherwise, you can go to Plugin_Directory/public/css/vize-tests-public.css to modify any style. (Not Recommended).

== Screenshots ==
1. Manage Tests
2. Add/Edit a Test
3. Manage Questions
4. Add/Edit a Question
5. Shortcode format
6. Test form in post or page
7. Test form and results if someone pass
8. Test form and results if someone fail

Please recommend and rate plugin if you like it.

Thank you

