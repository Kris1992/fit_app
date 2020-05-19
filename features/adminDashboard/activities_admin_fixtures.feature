Feature: Activities Admin Panel 
    In order to maintain the activities on the site
    As an admin
    I need to be able to add/edit/delete activities
    
    Background:
        Given Is database with "activities"
        Given I am logged in as an admin
        And I am on "/admin/activity"

    @fixtures
    Scenario: Edit an activity
        And I press "Edit" in the row with name "Push-ups" and "Normal" intensity
        And I fill in "activity_form[name]" with "Test"
        And I fill in "activity_form[energy]" with "100"
        And I select "High" from "activity_form[intensity]"
        And I fill in "activity_form[repetitionsAvgMin]" with "1"
        And I fill in "activity_form[repetitionsAvgMax]" with "5"
        And I press "Update"
        And I wait for the page to be loaded
        Then I should see "Activity is updated!" 

    @fixtures
    Scenario: Delete an activity
        And I press "Delete" in the row with name "Push-ups" and "Normal" intensity
        And I confirm the popup
        And I wait for the page to be loaded
        Then I should see "Activity was deleted!"

    @fixtures
    Scenario: Delete few activities
        And I check "deleteId[]" in the row with name "Push-ups" and "Normal" intensity
        And I check "deleteId[]" in the row with name "Push-ups" and "Low" intensity
        And I press "Delete checked"
        And I wait for the page to be loaded
        Then I should see "Activities were deleted!"

