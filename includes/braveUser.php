<?php
include("user_field.php");
class bcuf_braveUser
{
    private $userfield;
    function addField($userfield)
    {
        add_action('show_user_profile', [$userfield, 'showField']);
        add_action('edit_user_profile', [$userfield, 'showField']);
        add_action('user_new_form', [$userfield, 'showNewField']);
        add_action('edit_user_profile_update', [$userfield, 'updateFieldIntoDatabase']);
        add_action('personal_options_update', [$userfield, 'updateFieldIntoDatabase']);
        
        // Adding fields to the User Listing.
        if ($userfield->getShowOnUserListing()) {
            add_action('manage_users_columns', [$userfield, 'manage_users_columns']);
            add_action('manage_users_custom_column', [$userfield, 'manage_users_custom_column'],10,3);
            add_action('manage_users_sortable_columns', [$userfield, 'manage_users_sortable_columns']);
        }


        if ($userfield->getShowOnRegistration()) {
          //  $userfield->setPostFixHTML("");
            add_action('register_form', [$userfield, 'showNewField']);
            add_action('user_register', [$userfield, 'updateFieldIntoDatabase']);
        }        
    }
}