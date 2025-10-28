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

This website currently uses 3 entities : Tutorial ([Objet]), Library ([Inventaire])
and Theme ([Gallerie]). 

- The goal of Tutorial is obvious.

- The goal of Library is to list the tutorials of one author.

- The goal of Theme is to create a collection tutorials, sorted by a specific theme.

# 4) Routes

- The homepage can be found on :

localhost:8000/

- The list of all Tutorial entities can be found on :

localhost:8000/tutorial

- The list of all Library entities can be found on :

localhost:8000/library

- The list of all Theme entities can be found on :

localhost:8000/theme

- The list of all Member entities can be found on :

localhost:8000/member
