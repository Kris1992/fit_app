Feature: Sendind Emails 
    In order to contact with users and give them possibility to reset password 
    As an admin
    I need to be able to send emails
    
    Background:
        Given I am logged in as an admin

    #base on js
    @javascript
    Scenario: Add an activity
        And I am on "/admin/activity"
        And I click "Create"
        And I select "Bodyweight" from "activity_form[type]"
        And I fill in "activity_form[name]" with "Pussh-ups"
        And I fill in "activity_form[energy]" with "100"
        And I select "Normal" from "activity_form[intensity]"
        And I fill in "activity_form[repetitionsAvgMin]" with "20"
        And I fill in "activity_form[repetitionsAvgMax]" with "40"
        And I press "Create"
        And I wait for the page to be loaded
        Then I should see "Activity was created!"
        And I should see 1 row in the table
