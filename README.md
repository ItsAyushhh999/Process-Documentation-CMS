# Process Documentation CMS (TDMS)
#change in prod
Process Documentation CMS is an internal in-house project designed to streamline and centralize the process of creating and managing tasks, along with related documentation, for our organization.
 
Our platform acts as a central hub for both task and document management, offering employees easy access to the necessary resources to complete their work efficiently. The task management system allows users with appropriate permissions to create, assign, and track tasks assigned to different users or employees. This system enables a clear understanding of task ownership and accountability while also providing a means to track progress and identify potential roadblocks or issues.

In addition, the platform's document management system allows employees to create and organize documents required for their tasks. While all employees have access to view relevant documents, only users with appropriate permissions are authorized to edit or upload new documents. By ensuring that documentation is organized and easily accessible, the platform enables employees to quickly find the information they need to complete their work..


## Rules The Contributor Must Follow
___

* Always work on separate branches when you're assigned a task. <br>
  
* When you've completed a task, remember to push the branch containing your work to the main project, but avoid merging it with your main project. 

* When working in task with another member, push the branch only after the collaborative completion of the assigned task.
  
It's a good practice to follow this workflow, as it helps to ensure that only high-quality code ends up in the main project.
## Steps to setup the project: 
___
### Please do go through all the steps without skipping any one. 

<b>Follow the steps given:</b> 

><b>Important:</b> Either you are CUI freak or GUI lover feel free to use any of the methods. 

1. Fork the `process-documentation-cms` project into your **Github repository**.

2. Clone the forked `process-documentation-cms` from your repo into your local machine. 

    git clone https://github.com/<your_github_username>process-documentation-cms

    Or,

    git clone git@github.com:<your_github_username>/process-documentation-cms.git

    When you clone a repository, you may be prompted to enter a Personal Access Token. You can generate one by going to the Settings menu, then selecting Developer Settings. From there, you’ll be able to create a new token for use during the cloning process. 
  
 
3. In the terminal use the command **cd** to change into the directory the project is located in. 
   
    ```
    cd <project_name>
    ```

4. Copy the `.env copy.example` and rename it into `.env` using the command

    ```
    cp .env copy.example .env
    ```

    > If you find it difficult using commands, you can copy, paste and rename the file into `.env` the usual way.
 
    **Q. What if the `.env copy.example` does not exist?** <br>

    => If the .env copy.example does not exist, just Copy, Paste and Run the following command.

    * For LINUX/UNIX or Windows with GitBash installed:

    ```
    curl https://raw.githubusercontent.com/laravel/laravel/master/.env.example -o .env
    ``` 
    <br>

    * For Windows with PowerShell:

    ```
    Invoke-WebRequest -OutFile .env -Uri https://raw.githubusercontent.com/laravel/laravel/master/.env.example
    ```

5. Install <b>Composer</b> using the command:

    ```
    composer install
    ```

    > If the system prompts you to update the composer, please follow the instructions to do so.
  
6. Install <b>Npm</b> using the command:

    ```
    npm install
    ```

    > If the system prompts you to update the npm, please follow the instructions to do so.
 
7. Now you need to generate an <b>App encryption key</b>. For this run the command:

    ```
    php artisan key:generate
    ```

8. You also need to create a **Database** for this you can use any of the methods you  prefer. 
 
9. Since you already have created `.env` file, add your <b>Database</b> information into the `.env` file you have recently created. 

   ``` 
    DB_DATABASE=<your_database_name_for_the_project>

    DB_ USERNAME=<your_database_username>

    DB_PASSWORD=<your_database_password>
    ```

10. <b>Migrate</b> data into your <b>Database</b> using the command.
    
    ```
    php artisan migrate 
    ```
 
11. The **admin authentication** for the project is based on the **google authentication** of **shikhartech’s gmail id** so for that you need to add the following into the `.env` file. 

    ```
    GOOGLE_CLIENT_ID=your-google-client-id-here
    GOOGLE_CLIENT_SECRET=your-google-client-secret-here
    ```


12. **Now here is the crucial part.** Your local setup of the project may not work if you do not follow the instructions properly. You have to open up two terminals. Yes, you read it right, open up two terminals. In the 1st terminal, run the code ``npm run dev``, and in the 2nd terminal, run ``php artisan serve``.

    ```
    npm run dev
    ```
    And,

    ```
    php artisan serve
    ```

     ### Now comes the important final 13th step.

13. Copy the link generated after running the ``php artisan serve`` and paste it into the `.env` . You may choose to terminate the **SERVER** or choose not to, but the important thing is to paste the link into you `.env` file. 

    ```
    APP_URL=<Link_generated_from_running_the_php_artisan_Serve>
    ```
If you have terminated the server, do run it using the command `php artisan serve` or else click on the link. 
<br
### Congratulations!!! You have fully setup the project locally into your machine if you have strictly followed the steps. If the project does not runs as expected in your local machines, you may need help of any of your colleague currently working on it. Its a good to ask for help and guidance if you are new on it. It helps to strength the bond.
