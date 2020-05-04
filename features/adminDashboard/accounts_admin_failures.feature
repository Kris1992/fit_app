Feature: Accounts Admin Panel 
    In order to maintain the accounts registered on the site
    As an admin
    I need to be able to see error messages when something goes wrong
    
    To Do
    Background:
        Given I am logged in as an admin

    Scenario: Add an account
        And I am on "/admin/account"
        And I click "Create"
        And I fill in "user_registration_form[email]" with "userabc0@fit.com"
        And I fill in "user_registration_form[firstName]" with "Krzysztof"
        And I fill in "user_registration_form[secondName]" with "Rej"
        And I fill in "user_registration_form[plainPassword][first]" with "admin01"
        And I fill in "user_registration_form[plainPassword][second]" with "admin01"

        #Built in function for radio buttons don't exist so I used function to select buttons
        And I select "Male" from "user_registration_form_gender_0"
        And I check "user_registration_form[agreeTerms]"
        And I press "Register"
        And I wait for the page to be loaded
        And I press "userabc0@fit.com"
        Then I should see "Logout"

    @fixtures
    Scenario: Edit an account
        Given Is database with "users"
        And I am on "/admin/account"
        And I press "Edit" in the row with name "user0@fit.com"
        And I fill in "user_registration_form[email]" with "userabc0@fit.com"
        And I select "Admin" from "user_registration_form[role]"
        And I select "Female" from "user_registration_form_gender_0"
        And I fill in "user_registration_form[firstName]" with "Krzysztof"
        And I fill in "user_registration_form[secondName]" with "Rej"
        And I select "May" from "user_registration_form[birthdate][month]"
        And I select "1" from "user_registration_form[birthdate][day]"
        And I select "2000" from "user_registration_form[birthdate][year]"
        And I fill in "user_registration_form[weight]" with "70"
        And I fill in "user_registration_form[height]" with "150"
        And I press "Update"
        And I wait for the page to be loaded
        Then I should see "User is updated!" 

    @fixtures 
    Scenario: Delete an account
        Given Is database with "users"
        And I am on "/admin/account"
        And I press "Delete" in the row with name "user0@fit.com"
        And I confirm the popup
        And I wait for the page to be loaded
        Then I should see "User was deleted!"

    @fixtures
    Scenario: Delete few accounts
        Given Is database with "users"
        And I am on "/admin/account"
        And I check "deleteId[]" in the row with name "user0@fit.com"
        And I check "deleteId[]" in the row with name "user1@fit.com"
        And I press "Delete checked" 
        And I wait for the page to be loaded
        Then I should see "Users were deleted!"

        
        
