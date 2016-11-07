<?php
/**
Insert option to enter facebook and twitter username
*/
function fresh_form_system_theme_settings_alter(&$form, &$form_state) {

  $form['fresh_settings'] = array(
    '#type' => 'fieldset',
    '#title' => t('Fresh Theme Settings'),
    '#collapsible' => FALSE,
    '#collapsed' => FALSE,
  );
  $form['fresh_settings']['breadcrumbs'] = array(
    '#type' => 'checkbox',
    '#title' => t('Show breadcrumbs in a page'),
    '#default_value' => theme_get_setting('breadcrumbs', 'fresh'),
    '#description'   => t("Check this option to show breadcrumbs in page. Uncheck to hide."),
  );
  $form['fresh_settings']['top_social_link'] = array(
    '#type' => 'fieldset',
    '#title' => t('Social links in header'),
    '#collapsible' => TRUE,
    '#collapsed' => FALSE,
  );
  $form['fresh_settings']['top_social_link']['social_links'] = array(
    '#type' => 'checkbox',
    '#title' => t('Show twitter, facebook, rss links in header'),
    '#default_value' => theme_get_setting('social_links', 'fresh'),
    '#description'   => t("Check this option to show twitter, facebook, rss links in header. Uncheck to hide."),
  );
  $form['fresh_settings']['top_social_link']['twitter_username'] = array(
    '#type' => 'textfield',
    '#title' => t('Twitter Username'),
    '#default_value' => theme_get_setting('twitter_username', 'fresh'),
	'#description'   => t("Enter your Twitter username."),
  );
  $form['fresh_settings']['top_social_link']['facebook_username'] = array(
    '#type' => 'textfield',
    '#title' => t('Facebook Username'),
    '#default_value' => theme_get_setting('facebook_username', 'fresh'),
	'#description'   => t("Enter your Facebook username."),
  );
  $form['fresh_settings']['slideshow'] = array(
    '#type' => 'fieldset',
    '#title' => t('Front Page Slideshow'),
    '#collapsible' => TRUE,
    '#collapsed' => FALSE,
  );
  $form['fresh_settings']['slideshow']['slideshow_display'] = array(
    '#type' => 'checkbox',
    '#title' => t('Show slideshow'),
    '#default_value' => theme_get_setting('slideshow_display','fresh'),
    '#description'   => t("Check this option to show Slideshow in front page. Uncheck to hide."),
  );
    $form['fresh_settings']['slideshow']['slide'] = array(
    '#markup' => t('You can change the URL of each slide in the following Slide Setting fields.'),
  );
  $form['fresh_settings']['slideshow']['slide1']['slide1_url'] = array(
    '#type' => 'textfield',
    '#title' => t('Slide 1 URL'),
    '#default_value' => theme_get_setting('slide1_url','fresh'),
  );
  $form['fresh_settings']['slideshow']['slide2']['slide2_url'] = array(
    '#type' => 'textfield',
    '#title' => t('Slide 2 URL'),
    '#default_value' => theme_get_setting('slide2_url','fresh'),
  );
  $form['fresh_settings']['slideshow']['slide3']['slide3_url'] = array(
    '#type' => 'textfield',
    '#title' => t('Slide 3 URL'),
    '#default_value' => theme_get_setting('slide3_url','fresh'),
  );
  $form['fresh_settings']['slideshow']['slideimage'] = array(
    '#markup' => t('To change the Slide Images, Replace the slide-image-1.jpg, slide-image-2.jpg and slide-image-3.jpg in the images folder of the Fresh theme folder.'),
  );
  $form['fresh_settings']['footer'] = array(
    '#type' => 'fieldset',
    '#title' => t('Footer'),
    '#collapsible' => TRUE,
    '#collapsed' => FALSE,
  );
  $form['fresh_settings']['footer']['footer_copyright'] = array(
    '#type' => 'checkbox',
    '#title' => t('Show copyright text in footer'),
    '#default_value' => theme_get_setting('footer_copyright','fresh'),
    '#description'   => t("Check this option to show copyright text in footer. Uncheck to hide."),
  );
  $form['fresh_settings']['footer']['footer_credits'] = array(
    '#type' => 'checkbox',
    '#title' => t('Show theme credits in footer'),
    '#default_value' => theme_get_setting('footer_credits','fresh'),
    '#description'   => t("Check this option to show site credits in footer. Uncheck to hide."),
  );
}
?>