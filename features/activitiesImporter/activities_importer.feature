Feature: Import activities from csv file
    In order to maintain the activities avaible on the site
    As an admin
    I need to be able to import activities from file
    
    Background:
        Given I am logged in as an admin
        And I am on "/admin/activity"
        And I press "Import from file"
        And I wait for the page to be loaded

    @javascript @file @n
    Scenario: Import acticities from valid csv file with 2 activities
        Then I attach the file "csv/valid_csv.txt" to the dropzone
        And I wait for the page to be loaded
        And I press "Close"
        Then I reload the page
        And I wait for the page to be loaded
        And I should see 2 row in the table 

    @javascript @file
    Scenario: Import acticities from half valid (1 valid one not) csv file with 2 activities
        Then I attach the file "csv/half_valid_csv.txt" to the dropzone
        And I wait for the page to be loaded
        Then I should see "The activity with the same name and intensity already exist"
        And I press "Close"
        Then I reload the page
        And I wait for the page to be loaded
        And I should see 1 row in the table 

    @javascript @file
    Scenario: Import acticities from invalid file
        Then I attach the file "image/test.jpg" to the dropzone
        And I wait for the page to be loaded
        Then I should see "Please upload a valid CSV file"
        And I press "Close"
        Then I reload the page
        And I wait for the page to be loaded
        And I should see 0 row in the table 



#   features/activitiesImporter/activities_importer.feature:13
#    features/activitiesImporter/activities_importer.feature:22

