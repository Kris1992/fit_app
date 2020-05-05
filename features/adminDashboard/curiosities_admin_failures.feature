Feature: Failures in curiosities admin panel 
    In order to maintain the curiosities created on the site
    As an admin
    I need to be able to see proper error messages when something goes wrong
    
    Background:
        Given I am logged in as an admin

    Scenario: Try to delete few curiosities without check any curiosity to delete
        And I am on "/admin/curiosity"
        And I press "Delete checked" 
        And I wait for the page to be loaded
        Then I should see "Nothing to do."

        
        
