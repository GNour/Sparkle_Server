<p align="center"><a href="https://sparkletms.vercel.app/" target="_blank"><img src="https://github.com/GNour/Sparkle_Server/blob/main/documentations/Sparkle_Logo.png?raw=true" width="200"></a></p>

## About Sparkle Server

Sparkle Server serves as the backend of Sparkle Team Management System.

### Get started with simple steps

-   Clone repo, or download it as a .zip file
-   Open terminal and navigate to cloned folder
-   Run `composer update`
-   After composer have been updated, sometimes you might need to do `composer install`
-   Run `php artistan migrate`, If you already migrated the database before and need a fresh migrate you can run `php artisan migrate:fresh`
-   For dummy data, You can seed the tables from the create factories, Run `php artistan db:seed`

**Contact me** to send you the correct declarations in the **.env** file and the public API keys.
Any help needed, feel free to open an issue or email me directly [ghyathnour@gmail.com](mailto:ghyathnour@gmail.com).

## Database ERD
![alt text](https://github.com/GNour/Sparkle_Server/blob/main/documentations/Sparkle_Server_ERD.png?raw=true)

## APIs
- U.P: User Policy
- C.X: Verification happens in controller


| API | Staff | Managers |Common|
| ----------- | ----------- | ----------- | ----------- |
|login|||X|
|register||X||
|logout|||X|
|fetchAllTeams||X||
|createTeam||X||
|fetchAllUsers||X||
|fetchUser|U.P@view|X||
|deleteUser||X||
|fetchAllTasks|C.X|X||
|createTask||X||
|assignTask||X||
|unassignTask||X||
|finishTask||X||
|completeTask|C.X|||
|createCourse||X||
|createVideo||X||
|createArticle||X||
|createQuiz||X||
|createQuestion||X||
|deleteVideo||X||
|deleteArticle||X||
|deleteQuiz||X||
|deleteQuestion||X||
|startCourse|C.X|||
|startVideo|C.X|||
|startArticle|C.X|||
|startQuiz|C.X|||
|completeCourse|C.X|||
|completeVideo|C.X|||
|completeArticle|C.X|||
|completeQuiz|C.X|||
|fetchMessages|||X|
|sendMessage|||X|

### Server APIs - Key protected APIs
- fetchTaskableCourses
- fetchUsersBasicInfo
- getTeamsBasicInfo
- getManagers

### IOT APIs - Key protected APIs
- attend
- leave


## External packages
- tyson/jwt-auth - For JSON Web Token autorization
- Pusher - For live chat and notifications

**Check the source code for extra information (Modals, Controllers, Policies, Factories, Events...)**

**Please don't hesitate to reach out for furthur information**
