Feature: Accounts Admin Panel 
    In order to maintain the accounts registered on the site
    As an admin
    I need to be able to add/edit/delete accounts
    
    Background:
        Given I am logged in as an admin
        And I am on "/admin/account"
        And I click "Create"

    Scenario: Add an account 
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
        And break
        And I press "userabc0@fit.com"
        Then I should see "Logout"

    @file 
    Scenario: Add an account with image
        And I fill in "user_registration_form[email]" with "userabc0@fit.com"
        And I fill in "user_registration_form[firstName]" with "Krzysztof"
        And I fill in "user_registration_form[secondName]" with "Rej"
        And I fill in "user_registration_form[plainPassword][first]" with "admin01"
        And I fill in "user_registration_form[plainPassword][second]" with "admin01"
        Then I attach the file "image/test.jpg" to "user_registration_form[imageFile]"

        #Built in function for radio buttons don't exist so I used function to select buttons
        And I select "Male" from "user_registration_form_gender_0"
        And I check "user_registration_form[agreeTerms]"
        And I press "Register"
        And I wait for the page to be loaded
        And I press "userabc0@fit.com"
        Then I should see "Logout"


