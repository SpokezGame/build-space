# Build Space

# Summary

## 1) Introduction
## 2) Starting
## 3) Model
## 4) Routes

# 1) Introduction

This Web application permits everyone to posts and consults tutorials of builds in Minecraft.

You can find the TODO list on the directory build-space.

# 2) Starting

To start the website, just go to the build-space directory and execute :

*symfony server:start*

To re-load the fixtures execute : 

*symfony console doctrine:database:drop --force*

*symfony console doctrine:database:create*

*symfony console doctrine:schema:create*

*symfony console doctrine:fixtures:load -n*

# 3) Model

This website currently uses 2 entity : Tutorial ([Objet]) and ListTutorial ([Inventaire]). 

- The goal of Tutorial is obvious.

- The goal of ListTutorial is to list the tutorials of one author.

# 4) Routes

- The homepage can be found on :

localhost:8000/

- The list of all Tutorial entities can be found on :

localhost:8000/tutorial/all

- The list of all ListTutorials entities can be found on :

localhost:8000/list/tutorials
