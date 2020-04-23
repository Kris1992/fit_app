Feature: Workouts Admin Panel 
    In order to maintain the workouts on the site
    As an admin
    I need to be able to add/edit/delete workouts
    
    Background:
        Given I am logged in as an admin

    #base on js
    @javascript @fixtures
    Scenario: Add a movement workout
        Given Is database with "activities"
        And I am on "/admin/workout"
        And I click "Create"
        And I select "Cycling" from "activity"
        And I fill in "user" with "admin0@fit.com"
        And I fill in "startAt" with "2020-04-23 16:22"
        And I select "01" from "durationSecondsTotal[hour]"
        And I select "00" from "durationSecondsTotal[minute]"
        And I select "00" from "durationSecondsTotal[second]"
        And I press "Add"
        And I wait for the page to be loaded
        Then I should see "Workout was created!"
        And I should see 1 row in the table

    #base on js
    @javascript @fixtures
    Scenario: Add a bodyweight workout
        Given Is database with "activities"
        And I am on "/admin/workout"
        And I click "Create"
        And I select "Push-ups" from "activity"
        And I fill in "user" with "admin0@fit.com"
        And I fill in "startAt" with "2020-04-23 16:22"
        And I select "01" from "durationSecondsTotal[hour]"
        And I select "00" from "durationSecondsTotal[minute]"
        And I select "00" from "durationSecondsTotal[second]"
        And I press "Add"
        And I wait for the page to be loaded
        Then I should see "Workout was created!"
        And I should see 1 row in the table

    #base on js
    @javascript @fixtures @new
    Scenario: Add a movement with sets workout
        Given Is database with "activities"
        And I am on "/admin/workout"
        And I click "Create"
        And I select "Cycling circuits" from "activity"
        And I fill in "user" with "admin0@fit.com"
        And I fill in "startAt" with "2020-04-23 16:22"
        And I click "Add set"
        And I select "Cycling" from "movementSets[0][activity]"
        And I select "01" from "movementSets[0][durationSeconds][hour]"
        And I select "00" from "movementSets[0][durationSeconds][minute]"
        And I select "00" from "movementSets[0][durationSeconds][second]"
        And I click "Add set"
        And I select "Cycling" from "movementSets[1][activity]"
        And I select "02" from "movementSets[1][durationSeconds][hour]"
        And I select "30" from "movementSets[1][durationSeconds][minute]"
        And I select "30" from "movementSets[1][durationSeconds][second]"
        And I press "Add"
        And I wait for the page to be loaded
        Then I should see "Workout was created!"
        And I should see 1 row in the table

    #base on js
    @javascript @fixtures
    Scenario: Add a movement workout
        Given Is database with "activities"
        And I am on "/admin/workout"
        And I click "Create"
        And I select "Cycling" from "activity"
        And I fill in "user" with "admin0@fit.com"
        And I fill in "startAt" with "2020-04-23 16:22"
        And I select "01" from "durationSecondsTotal[hour]"
        And I select "00" from "durationSecondsTotal[minute]"
        And I select "00" from "durationSecondsTotal[second]"
        And I press "Add"
        And I wait for the page to be loaded
        Then I should see "Workout was created!"
        And I should see 1 row in the table

    @fixtures @new
    Scenario: Edit a bodyweight workout
        Given Is database with "workouts"
        #And I am on "/admin/activity"
        #And I press "Edit" in the row with name "Push-ups" and "Normal" intensity
        #And I fill in "activity_form[name]" with "Test"
        #And I fill in "activity_form[energy]" with "100"
    #    And I select "High" from "activity_form[intensity]"
    #    And I fill in "activity_form[repetitionsAvgMin]" with "1"
    #    And I fill in "activity_form[repetitionsAvgMax]" with "5"
    #    And I press "Update"
    #    And I wait for the page to be loaded
    #    Then I should see "Activity is updated!" 

    #@fixtures
    #Scenario: Delete an activity
    #    Given Is database with "activities"
    #    And I am on "/admin/activity"
    #    And I press "Delete" in the row with name "Push-ups" and "Normal" intensity
    #    And I confirm the popup
    #    And I wait for the page to be loaded
    #    Then I should see "Activity was deleted!"



   

        
        
