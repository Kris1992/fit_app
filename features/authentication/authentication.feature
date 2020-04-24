Feature: Authentication
    In order to gain access to the site management area
    As an admin
    I need to be able to login and logout
    
    @javascript
    Scenario: Logging in
        Given there is an admin user "admin0Test@fit.com" with password "admin01"
        And I am on "/login"
        And I fill in "email" with "admin0Test@fit.com"
        And I fill in "password" with "admin01"
        #And I save a screenshot to "before_login.png"
        And I press "Sign in"
        And I wait for the page to be loaded
        And I press "admin0Test@fit.com"
        #And I save a screenshot to "after_login.png"
        Then I should see "Logout"
