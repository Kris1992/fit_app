Feature: Register
    In order to gain access to the main functionality of website 
    As an anonymous user
    I need to be able to create new account
    
    @javascript
    Scenario: Create new account
        And I am on "/register"
        And I fill in "user_registration_form[email]" with "userabc0@fit.com"
        And I fill in "user_registration_form[firstName]" with "Krzysztof"
        And I fill in "user_registration_form[secondName]" with "Rej"
        And I fill in "user_registration_form[plainPassword][first]" with "admin01"
        And I fill in "user_registration_form[plainPassword][second]" with "admin01"

        #Built in function for radio buttons don't exist so I used function to select buttons
        And I select "Male" from "user_registration_form_gender_0"
        And I check "user_registration_form[agreeTerms]"
        #And break
        #And I save a screenshot to "before_register.png"
        And I press "Register"
        And I wait for the page to be loaded
        And break
        And I press "userabc0@fit.com"
        #And I save a screenshot to "after_register.png"
        Then I should see "Logout"
        