Feature: Failures in accounts admin panel 
    In order to maintain the accounts registered on the site
    As an admin
    I need to be able to see proper error messages when something goes wrong
    
    Background:
        Given I am logged in as an admin
        And I am on "/admin/account"
    
    Scenario: Try to add an account (Too weak password message)
        And I click "Create"
        And I fill in "user_registration_form[email]" with "userabc0@fit.com"
        And I fill in "user_registration_form[firstName]" with "Krzysztof"
        And I fill in "user_registration_form[secondName]" with "Rej"
        And I fill in "user_registration_form[plainPassword][first]" with "Testadmin"
        And I fill in "user_registration_form[plainPassword][second]" with "Testadmin"
        And I select "Male" from "user_registration_form_gender_0"
        And I press "Register"
        And I wait for the page to be loaded
        Then I should see "Password should contain at least 2 numbers and 3 letters"
        
    Scenario: Try to delete few accounts without check any user to delete
        And I press "Delete checked" 
        And I wait for the page to be loaded
        Then I should see "Nothing to do."
