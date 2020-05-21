Feature: Workouts Admin Panel 
    In order to maintain the workouts on the site
    As an admin
    I need to be able to add/edit/delete workouts

    #base on js
    @javascript @fixtures 
    Scenario: Add a movement workout with average data
        Given Is database with "activities"
        Given I am logged in as an admin
        And I am on "/admin/workout"
        And I click "Create"
        And I select "Cycling" from "activity"
        And I fill in "user" with "admin0Test@fit.com"
        And I fill in "startAt" with "2020-04-23 16:22"
        And I select "01" from "durationSecondsTotal[hour]"
        And I select "00" from "durationSecondsTotal[minute]"
        And I select "00" from "durationSecondsTotal[second]"
        And I press "Add"
        And I wait for the page to be loaded
        Then I should see "Workout was created!"
        And I should see 1 row in the table

    #base on js
    @javascript @fixtures @file
    Scenario: Add a movement workout with average data and image
        Given Is database with "activities"
        Given I am logged in as an admin
        And I am on "/admin/workout"
        And I click "Create"
        And I select "Cycling" from "activity"
        And I fill in "user" with "admin0Test@fit.com"
        Then I attach the file "image/test.jpg" to "imageFile"
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
    Scenario: Add a movement workout with specific data
        Given Is database with "activities"
        Given I am logged in as an admin
        And I am on "/admin/workout/add_specific"
        And I select "Cycling" from "activityName"
        And I fill in "user" with "admin0Test@fit.com"
        And I fill in "startAt" with "2020-04-23 16:22"
        And I select "01" from "durationSecondsTotal[hour]"
        And I select "00" from "durationSecondsTotal[minute]"
        And I select "00" from "durationSecondsTotal[second]"
        And I fill in "distanceTotal" with "15"
        And I press "Add"
        And I wait for the page to be loaded
        Then I should see "Workout was created!"
        And I should see 1 row in the table

    #base on js
    @javascript @fixtures @file
    Scenario: Add a movement workout with specific data and image
        Given Is database with "activities"
        Given I am logged in as an admin
        And I am on "/admin/workout/add_specific"
        And I select "Cycling" from "activityName"
        And I fill in "user" with "admin0Test@fit.com"
        Then I attach the file "image/test.jpg" to "imageFile"
        And I fill in "startAt" with "2020-04-23 16:22"
        And I select "01" from "durationSecondsTotal[hour]"
        And I select "00" from "durationSecondsTotal[minute]"
        And I select "00" from "durationSecondsTotal[second]"
        And I fill in "distanceTotal" with "15"
        And I press "Add"
        And I wait for the page to be loaded
        Then I should see "Workout was created!"
        And I should see 1 row in the table

    #base on js
    @javascript @fixtures 
    Scenario: Add a bodyweight workout with average data
        Given Is database with "activities"
        Given I am logged in as an admin
        And I am on "/admin/workout"
        And I click "Create"
        And I select "Push-ups" from "activity"
        And I fill in "user" with "admin0Test@fit.com"
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
    Scenario: Add a bodyweight workout with specific data
        Given Is database with "activities"
        Given I am logged in as an admin
        And I am on "/admin/workout/add_specific"
        And I select "Push-ups" from "activityName"
        And I fill in "user" with "admin0Test@fit.com"
        And I fill in "startAt" with "2020-04-23 16:22"
        And I select "01" from "durationSecondsTotal[hour]"
        And I select "00" from "durationSecondsTotal[minute]"
        And I select "00" from "durationSecondsTotal[second]"
        And I fill in "repetitionsTotal" with "70"
        And I press "Add"
        And I wait for the page to be loaded
        Then I should see "Workout was created!"
        And I should see 1 row in the table

    #base on js
    @javascript @fixtures 
    Scenario: Add a weight workout with average data
        Given Is database with "activities"
        Given I am logged in as an admin
        And I am on "/admin/workout"
        And I click "Create"
        And I select "Barbell Bench Press" from "activity"
        And I fill in "user" with "admin0Test@fit.com"
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
    Scenario: Add a weight workout with specific data
        Given Is database with "activities"
        Given I am logged in as an admin
        And I am on "/admin/workout/add_specific"
        And I select "Barbell Bench Press" from "activityName"
        And I fill in "user" with "admin0Test@fit.com"
        And I fill in "startAt" with "2020-04-23 16:22"
        And I select "01" from "durationSecondsTotal[hour]"
        And I select "00" from "durationSecondsTotal[minute]"
        And I select "00" from "durationSecondsTotal[second]"
        And I fill in "repetitionsTotal" with "50"
        And I fill in "dumbbellWeight" with "40"
        And I press "Add"
        And I wait for the page to be loaded
        Then I should see "Workout was created!"
        And I should see 1 row in the table

    #base on js
    @javascript @fixtures @nem
    Scenario: Add a movement with sets workout with average data
        Given Is database with "activities"
        Given I am logged in as an admin
        And I am on "/admin/workout"
        And I click "Create"
        And I select "Cycling circuits" from "activity"
        And I fill in "user" with "admin0Test@fit.com"
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
    Scenario: Add a movement with sets workout with specific data
        Given Is database with "activities"
        Given I am logged in as an admin
        And I am on "/admin/workout/add_specific"
        And I select "Cycling circuits" from "activityName"
        And I fill in "user" with "admin0Test@fit.com"
        And I fill in "startAt" with "2020-04-23 16:22"
        And I click "Add set"
        And I select "Cycling" from "movementSets[0][activityName]"
        And I select "01" from "movementSets[0][durationSeconds][hour]"
        And I select "00" from "movementSets[0][durationSeconds][minute]"
        And I select "00" from "movementSets[0][durationSeconds][second]"
        And I fill in "movementSets[0][distance]" with "18"
        And I click "Add set"
        And I select "Cycling" from "movementSets[1][activityName]"
        And I select "01" from "movementSets[1][durationSeconds][hour]"
        And I select "00" from "movementSets[1][durationSeconds][minute]"
        And I select "00" from "movementSets[1][durationSeconds][second]"
        And I fill in "movementSets[1][distance]" with "18"
        And I press "Add"
        And I wait for the page to be loaded
        Then I should see "Workout was created!"
        And I should see 1 row in the table

    @fixtures
    Scenario: Edit a bodyweight workout by average data form 
        Given Is database with "workouts"
        Given I am logged in as an admin
        And I am on "/admin/workout"
        Then I fill in "filterValue" with "Push"
        And I press "Search"
        And I press "Edit" in the row with name "Push-ups"
        And I fill in "startAt" with "2020-04-23 16:22"
        And I select "02" from "durationSecondsTotal[hour]"
        And I select "30" from "durationSecondsTotal[minute]"
        And I select "30" from "durationSecondsTotal[second]"
        And I press "Update"
        And I wait for the page to be loaded
        Then I should see "Workout was updated!" 

    @fixtures @file
    Scenario: Edit a bodyweight workout by average data form and image
        Given Is database with "workouts"
        Given I am logged in as an admin
        And I am on "/admin/workout"
        Then I fill in "filterValue" with "Push"
        And I press "Search"
        And I press "Edit" in the row with name "Push-ups"
        Then I attach the file "image/test.jpg" to "imageFile"
        And I fill in "startAt" with "2020-04-23 16:22"
        And I select "02" from "durationSecondsTotal[hour]"
        And I select "30" from "durationSecondsTotal[minute]"
        And I select "30" from "durationSecondsTotal[second]"
        And I press "Update"
        And I wait for the page to be loaded
        Then I should see "Workout was updated!" 

    @fixtures
    Scenario: Edit a movement workout by average data form 
        Given Is database with "workouts"
        Given I am logged in as an admin
        And I am on "/admin/workout"
        Then I fill in "filterValue" with "Run"
        And I press "Search"
        And I press "Edit" in the row with name "Running"
        And I fill in "startAt" with "2020-04-23 16:22"
        And I select "02" from "durationSecondsTotal[hour]"
        And I select "30" from "durationSecondsTotal[minute]"
        And I select "30" from "durationSecondsTotal[second]"
        And I press "Update"
        And I wait for the page to be loaded
        Then I should see "Workout was updated!"

    @fixtures
    Scenario: Delete a workout
        Given Is database with "workouts"
        Given I am logged in as an admin
        And I am on "/admin/workout"
        Then I fill in "filterValue" with "Run"
        And I press "Search"
        And I press "Delete" in the row with name "Running"
        And I confirm the popup
        And I wait for the page to be loaded
        Then I should see "Workout was deleted!"

    @fixtures
    Scenario: Delete few workouts
        Given Is database with "workouts"
        Given I am logged in as an admin
        And I am on "/admin/workout"
        Then I fill in "filterValue" with "Push"
        And I press "Search"
        And I check first unchecked "deleteId[]" in the row with name "Push-ups"
        And I check first unchecked "deleteId[]" in the row with name "Push-ups"
        And I press "Delete checked"
        And I wait for the page to be loaded
        Then I should see "Workouts were deleted!"

#    features/adminDashboard/workouts_admin_fixtures.feature:155