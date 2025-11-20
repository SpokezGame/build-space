# Build Space

# Summary

## 1) Introduction
## 2) Starting
## 3) Model
## 4) Routes
## 5) Navigation
## 6) Account
## 7) Remark

# 1) Introduction

This Web application permits everyone to posts and consults tutorials of builds in Minecraft.

In each tutorial, there is a list of images that represents the steps of the build.

And tutorials can be grouped by themes.

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

This website currently uses 4 entities : Tutorial ([Objet]), Library ([Inventaire]), Theme ([Gallerie]) and Member ([User]).
It uses also Image to keep in the database all the images. 

- The goal of Tutorial is to show the steps to build a structure on Minecraft.

- The goal of Library is to list the tutorials of one author.

- The goal of Theme is to create a collection tutorials, sorted by a specific theme.

# 4) Routes

- The homepage can be found on :

localhost:8000/

Using this link is the right way to surf on this website. But, there are restriction to some pages if you don't have the access. For example, you can't go to the page to create a tutorial in a theme that you didn't create. It redirects you to the homepage.

- The list of all Tutorial entities can be found on :

localhost:8000/tutorial

- The list of all Library entities can be found on (You must be admin to access to that page) :

localhost:8000/library

- The list of all Theme entities can be found on :

localhost:8000/theme

- The list of all Member entities can be found on :

localhost:8000/member

# 5) Navigation

On the homepage, you'll find 3 link to the lists of tutorials, members and themes.

You can click on the logo BUILDSPACE to return to the homepage.

## Creating a tutorial

You can create a tutorial by logging in an account. 
Then go to members > the name of your account > your account library > New tutorial

## Edit a tutorial

You can edit a tutorial on the page of a tutorial (go to edit at the bottom).

## Creating a theme
You can create a theme by logging in an account. 
Then go to members > the name of your account > New theme

## Edit a theme

You can edit a theme on the page of a theme (go to edit at the bottom).

# 6) Account

To get logged in, click on login on the navigation bar, then enter one of the following IDs :

**Email : admin@localhost**

**Password : admin123**

Name : admin

Role : Admin

**Email : spokez@localhost**

**Password : spokez123**

Name : spokez

Role : Member

**Email : lyanou@localhost**

**Password : lyanou123**

Name : lyanou

Role : Member

Be aware that when you're logged in to an admin account, you can do everything you want : create a tutorial for an another account for example.

A member can only create, edit or delete items (tutorials, themes) that they owned. Also, they can only see their own themes and published themes from other people. They can't see unpublished themes of other people.

A not logged in account can only see published themes. And can't create anything, nor edit or delete something.

# 7) Remark

There are examples of steps in Fantasy House, Well and Cupboard.