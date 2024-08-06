# HoopStats Project Proposal

## Demo Video
[Watch the demo](https://www.youtube.com/watch?v=Z2FBSs5UA6s)

## Milestone Submissions
- [Milestone 1](https://github.com/VikShah/vs53-it202-452/blob/prod/vs53_it202-module-6-milestone-1-2024_IT202-452-M2024%20copy.pdf)
- [Milestone 2](https://github.com/VikShah/vs53-it202-452/blob/prod/vs53_it202-api-project-milestone-2-2024-m24_IT202-452-M2024%20(1).pdf)
- [Milestone 3](https://github.com/VikShah/vs53-it202-452/blob/prod/vs53_it202-api-project-milestone-3-2024-m24_IT202-452-M2024%20(2).pdf)

## Project Overview
HoopStats is a comprehensive basketball statistics viewer designed to cater to both regular users and admins. The project allows users to view detailed player statistics, manage their favorite players, and provides admin functionalities for managing player and user associations. The main aim is to create an intuitive and user-friendly platform for basketball enthusiasts to track and manage player information.

## Features
- **User Authentication:** Secure login and registration system.
- **Profile Management:** Users can update their email, username, and password from their profile page.
- **Player List:** Displays a list of all players with options to filter, sort, and view detailed stats.
- **Favorites Management:** Users can add or remove players from their favorites and view their favorite players.
- **Admin Management:** 
  - **Manage Favorites:** Admins can view and manage user associations with players in bulk.
  - **Unassociated Players:** Lists players not currently associated with any user.
  - **Associate Entities:** Admins can search for players and users, then create associations between them.
  - **All User Associations:** Provides a detailed view of all associations in the system with options to filter and sort.

## Known Issues
- **Search Functionality:** The search functionality for partial matches on the associate entities page sometimes returns unexpected results due to case sensitivity issues.
- **Pagination Limits:** Pagination on the manage favorites and all user associations pages might not dynamically adjust when the number of results changes, potentially causing display issues.
- **Performance:** Fetching a large number of player statistics can cause performance lags, particularly when the API rate limit is hit.
- **UI Consistency:** Some UI elements may not be consistently styled across different pages, which can affect the user experience.
- **Session Handling:** Occasional issues with session handling, where users might get logged out unexpectedly due to session expiration.
