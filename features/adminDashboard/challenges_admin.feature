#Feature: Challenges Admin Panel 
#    In order to maintain the challenges created on the site
#    As an admin
#    I need to be able to add challenges
#    
#    Background:
#        Given I am logged in as an admin
#        And I am on "/admin/challenge"
#        And I click "Create"
#
#    @new 
#    Scenario: Add a challenge
#        And break
#        And I fill in "curiosity_form[title]" with "Simple title"
#        And I fill in "curiosity_form[description]" with "About me"
#        Then I attach the file "image/test.jpg" to "curiosity_form[imageFile]"
#        And I fill tinymce field with "Content of curiosity"
#        And I press "Add"
#        And I wait for the page to be loaded
#        Then I should see "Curiosity was created!"
#        And I should see 1 row in the table 
#        Then I press "Edit" in the row with name "Simple title"
#        And the "curiosity_form[isPublished]" checkbox should be unchecked
