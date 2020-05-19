Feature: Curiosities Admin Panel 
    In order to maintain the curiosities created on the site
    As an admin
    I need to be able to add/edit/delete curiosities
    
    Background:
        Given Is database with "curiosities"
        Given I am logged in as an admin
        And I am on "/admin/curiosity"

    #@fixtures 
    #Scenario: Edit a curiosity
        #Given Is database with "curiosities"
        #And I am on "/admin/curiosity"
        #And I click "Edit" on the first row
        #And break
        #And I fill in "curiosity_form[title]" with "Test title"
        #And I fill in "curiosity_form[description]" with "Test me"
        #And I fill tinymce field with "Content of curiosity Test"
        #And I press "Edit"
        #And I wait for the page to be loaded
        #Then I should see "Curiosity was updated!"
        

    #@fixtures 
    #Scenario: Delete an account
    #    Given Is database with "users"
    #    And I am on "/admin/account"
    #    And I press "Delete" in the row with name "user0@fit.com"
    #    And I confirm the popup
    #    And I wait for the page to be loaded
    #    Then I should see "User was deleted!"

    #@fixtures
    #Scenario: Delete few accounts
    #    Given Is database with "users"
    #    And I am on "/admin/account"
    #    And I check "deleteId[]" in the row with name "user0@fit.com"
    #    And I check "deleteId[]" in the row with name "user1@fit.com"
    #    And I press "Delete checked" 
    #    And I wait for the page to be loaded
    #    Then I should see "Users were deleted!"

        

            
