/*
RouteProvider.js (context: "ProjectAdministration"). Generated code, edit with care.
Generated by Ampersand v3.2.0[master:4b6fc5c*], build time: 06-Nov-15 20:29:59 Ame
*/
AmpersandApp.config( function ($routeProvider) {
  $routeProvider
    .when( '/Started_32_projects/:resourceId?' 
         , { controller:  'Started_32_projectsController'
           , templateUrl: 'generics/app/views/Started_32_projects.html'
           , interfaceLabel: 'Started projects' 
           }
         )
    .when( '/Unstarted_32_projects/:resourceId?' 
         , { controller:  'Unstarted_32_projectsController'
           , templateUrl: 'generics/app/views/Unstarted_32_projects.html'
           , interfaceLabel: 'Unstarted projects' 
           }
         )
    .when( '/People/:resourceId?' 
         , { controller:  'PeopleController'
           , templateUrl: 'generics/app/views/People.html'
           , interfaceLabel: 'People' 
           }
         )
    .when( '/Project/:resourceId?' 
         , { controller:  'ProjectController'
           , templateUrl: 'generics/app/views/Project.html'
           , interfaceLabel: 'Project' 
           }
         )
    .when( '/Person/:resourceId?' 
         , { controller:  'PersonController'
           , templateUrl: 'generics/app/views/Person.html'
           , interfaceLabel: 'Person' 
           }
         )
});