Feature: Curiosities Admin Panel 
    In order to maintain the curiosities created on the site
    As an admin
    I need to be able to add/edit/delete curiosities
    
    Background:
        Given Is database with "curiosities"
        Given I am logged in as an admin
        And I am on "/admin/curiosity"

    @fixtures 
    Scenario: Edit a curiosity
        And I press "Edit" in the row with name "1"
        And I fill in "curiosity_form[title]" with "Test title"
        And I fill in "curiosity_form[description]" with "Test me"
        And I fill tinymce field with "Content of curiosity Test"
        And I press "Edit"
        And I wait for the page to be loaded
        Then I should see "Curiosity was updated!"
        
    @fixtures 
    Scenario: Delete a curiosity
        And I press "Delete" in the row with name "1"
        And I confirm the popup
        And I wait for the page to be loaded
        Then I should see "Curiosity was deleted!"

    @fixtures 
    Scenario: Delete few curiosities
        And I check "deleteId[]" in the row with name "1"
        And I check "deleteId[]" in the row with name "2"
        And I press "Delete checked" 
        And I wait for the page to be loaded
        Then I should see "Curiosities were deleted!"

        

            
