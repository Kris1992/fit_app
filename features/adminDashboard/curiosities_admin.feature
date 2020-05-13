Feature: Curiosities Admin Panel 
    In order to maintain the curiosities created on the site
    As an admin
    I need to be able to add/edit/delete curiosities
    
    Background:
        Given I am logged in as an admin

    @file 
    Scenario: Add a unpublished curiosity with image
        And I am on "/admin/curiosity"
        And I click "Create"
        And I fill in "curiosity_form[title]" with "Simple title"
        And I fill in "curiosity_form[description]" with "About me"
        Then I attach the file "image/test.jpg" to "curiosity_form[imageFile]"
        And I fill tinymce field with "Content of curiosity"
        And I press "Add"
        And I wait for the page to be loaded
        Then I should see "Curiosity was created!"
        And I should see 1 row in the table 
        Then I press "Edit" in the row with name "Simple title"
        And the "curiosity_form[isPublished]" checkbox should be unchecked
    
    @file
    Scenario: Add a published curiosity with image
        And I am on "/admin/curiosity"
        And I click "Create"
        And I fill in "curiosity_form[title]" with "Simple title"
        And I fill in "curiosity_form[description]" with "About me"
        Then I attach the file "image/test.jpg" to "curiosity_form[imageFile]"
        And I fill tinymce field with "Content of curiosity"
        And I check "curiosity_form[isPublished]"
        And I press "Add"
        And I wait for the page to be loaded
        Then I should see "Curiosity was created!"
        And I should see 1 row in the table
        Then I press "Edit" in the row with name "Simple title"
        And the "curiosity_form[isPublished]" checkbox should be checked

    #@fixtures new
    #Scenario: Edit a curiosity
    #    Given Is database with "curiosities"
    #    And I am on "/admin/curiosity"
    #   # And I press "Edit" in the row with name "admin0@fit.com"
    #    And break
        

        #And I press "Update"
        #And I wait for the page to be loaded
        #Then I should see "User is updated!" 

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

        
    #features/adminDashboard/curiosities_admin.feature:23
    #features/adminDashboard/curiosities_admin.feature:37