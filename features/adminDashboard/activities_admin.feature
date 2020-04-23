Feature: Activities Admin Panel 
    In order to maintain the activities on the site
    As an admin
    I need to be able to add/edit/delete activities
    
    Background:
        Given I am logged in as an admin

    #base on js
    @javascript
    Scenario: Add an activity
        And I am on "/admin/activity"
        And I click "Create"
        And I select "Bodyweight" from "activity_form[type]"
        And I fill in "activity_form[name]" with "Push-ups"
        And I fill in "activity_form[energy]" with "100"
        And I select "Normal" from "activity_form[intensity]"
        And I fill in "activity_form[repetitionsAvgMin]" with "20"
        And I fill in "activity_form[repetitionsAvgMax]" with "40"
        And I press "Create"
        And I wait for the page to be loaded
        Then I should see "Activity was created!"
        And I should see 1 row in the table    

    @fixtures
    Scenario: Edit an activity
        Given Is database with "activities"
        And I am on "/admin/activity"
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
        Given Is database with "activities"
        And I am on "/admin/activity"
        And I press "Delete" in the row with name "Push-ups" and "Normal" intensity
        And I confirm the popup
        And I wait for the page to be loaded
        Then I should see "Activity was deleted!"

    @fixtures
    Scenario: Delete few activities
        Given Is database with "activities"
        And I am on "/admin/activity"
        And I check "deleteId[]" in the row with name "Push-ups" and "Normal" intensity
        And I check "deleteId[]" in the row with name "Push-ups" and "Low" intensity
        And I press "Delete checked"
        And I wait for the page to be loaded
        Then I should see "Activities were deleted!"
