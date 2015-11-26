/*
Controller for interface "Person" (context: "ProjectAdministration"). Generated code, edit with care.
*/
AmpersandApp.controller('PersonController', function ($scope, $rootScope, $route, $routeParams, Restangular, $location, $timeout, $localStorage, $sessionStorage) {
	$scope.interfaceName = "Person";
	$scope.interfaceLabel = "Person";
	$scope.resourceLabel = ''; // label of requested interface source resource
	
	$scope.val = {};
	$scope.initialVal = {};
	$scope.showSaveButton = {}; // initialize object for show/hide save button
	$scope.showCancelButton = {}; // initialize object for show/hide cancel button
	$scope.resourceStatus = {}; // initialize object for resource status colors
	$scope.loadingInterface = []; // array for promises, used by angular-busy module (loading indicator)
	$scope.loadingResources = {}; // initialize object for promises, used by angular-busy module (loading indicator)
	
	$scope.$localStorage = $localStorage;
	$scope.$sessionStorage = $sessionStorage;
	
	if(typeof $routeParams.resourceId != 'undefined') srcAtomId = $routeParams.resourceId;
	else srcAtomId = $sessionStorage.session.id;
	
	// BaseURL to the API is already configured in AmpersandApp.js (i.e. 'http://pathToApp/api/v1/')
	$scope.srcAtom = Restangular.one('resource/Person', srcAtomId);
	$scope.val['Person'] = new Array();
	
	if($routeParams['new']){
		$scope.loadingInterface.push(
			$scope.srcAtom.all('Person').post({})
				.then(function(data) { // POST
				$rootScope.updateNotifications(data.notifications);
				$scope.val['Person'].push(Restangular.restangularizeElement($scope.srcAtom, data.content, 'Person')); // Add to collection
				showHideButtons(data.invariantRulesHold, data.requestType, data.content.id);
			})
		);
	}else{
	    $scope.loadingInterface.push(
	    	$scope.srcAtom.all('Person').getList().then(function(data){
	    		if($.isEmptyObject(data.plain())){
	    			$rootScope.addInfo('No results found');
	    		}else{
	    			$scope.val['Person'] = data;
	    			$scope.resourceLabel = $scope.val['Person'][0]['@label'];
	    		}
	    	})
	    );
    }
	
	$scope.$on("$locationChangeStart", function(event, next, current){
		
		checkRequired = false; // default
		for(var item in $scope.showSaveButton) { // iterate over all properties (resourceIds) in showSaveButton object
			if($scope.showSaveButton.hasOwnProperty( item ) ) { // only checks its own properties, not inherited ones
				if($scope.showSaveButton[item] == true) checkRequired = true; // if item is not saved, checkRequired before location change
			}
		}
		
		if(checkRequired){ // if checkRequired (see above)
			confirmed = confirm("You have unsaved edits. Do you wish to leave?");
			if (event && !confirmed) event.preventDefault();
		}
	});
	
	// Function to create a new Resource
	$scope.newResource = function(){
		$location.url('/Person?new');
	}
	
	// Function to add a new Resource to the colletion
	$scope.addNewResource = function (prepend){
		if(prepend === 'undefined') var prepend = false;
		
		$scope.loadingResources['_new_'] = new Array();
		$scope.loadingResources['_new_'].push(
			$scope.srcAtom.all('Person')
				.post({})
				.then(function(data){ // POST
					$rootScope.updateNotifications(data.notifications);
					if(prepend) $scope.val['Person'].unshift(Restangular.restangularizeElement($scope.srcAtom, data.content, 'Person')); // Add to collection
					else $scope.val['Person'].push(Restangular.restangularizeElement($scope.srcAtom, data.content, 'Person')); // Add to collection
					showHideButtons(data.invariantRulesHold, data.requestType, data.content.id);
					$scope.loadingResources['_new_'] = new Array(); // empty arr
				})
		);
	}
	
	// Delete function to delete a complete Resource
	$scope.deleteResource = function (resourceId){
		if(confirm('Are you sure?')){
			var resourceIndex = _getResourceIndex(resourceId, $scope.val['Person']);
			
			// myPromise is used for busy indicator
			$scope.loadingResources[resourceId] = new Array();
			$scope.loadingResources[resourceId].push(
				$scope.val['Person'][resourceIndex]
					.remove({ 'requestType' : 'promise'})
					.then(function(data){
						$rootScope.updateNotifications(data.notifications);
						$scope.val['Person'].splice(resourceIndex, 1); // remove from array
					})
			);
		}
	}
	
	// Put function to update a Resource
	$scope.put = function(resourceId, requestType){
		var resourceIndex = _getResourceIndex(resourceId, $scope.val['Person']);
		requestType = requestType || $rootScope.defaultRequestType; // set requestType. This does not work if you want to pass in a falsey value i.e. false, null, undefined, 0 or ""
		
		// myPromise is used for busy indicator
		$scope.loadingResources[resourceId] = new Array();
	
		var location = $location.search();
		// if ?new => POST
		if(location['new']){
			$scope.loadingResources[resourceId].push(
				$scope.srcAtom.all('Person')
				.post($scope.val['Person'][resourceIndex].plain(), {'requestType' : requestType})
				.then(function(data) { // POST
					$rootScope.updateNotifications(data.notifications);
					$scope.val['Person'][resourceIndex] = $.extend($scope.val['Person'][resourceIndex], data.content);
					showHideButtons(data.invariantRulesHold, data.requestType, data.content.id);
					
					if(data.invariantRulesHold && data.requestType == 'promise'){
						// Resource is posted, change url
						$location.url('/Person/' + data.content.id);
					}
				})
			);
		// else => PUT
		}else{
			$scope.loadingResources[resourceId].push( $scope.val['Person'][resourceIndex]
				.put({'requestType' : requestType})
				.then(function(data) {
					$rootScope.updateNotifications(data.notifications);
					$scope.val['Person'][resourceIndex] = $.extend($scope.val['Person'][resourceIndex], data.content);
					showHideButtons(data.invariantRulesHold, data.requestType, resourceId);
				})
			);
		}
	}
	
	// Function to cancel edits and reset resource data
	$scope.cancel = function(resourceId){
		var resourceIndex = _getResourceIndex(resourceId, $scope.val['Person']);
		
		// myPromise is used for busy indicator
		$scope.loadingResources[resourceId] = new Array();
		$scope.loadingResources[resourceId].push(
			$scope.val['Person'][resourceIndex]
				.get()
				.then(function(data){
					$rootScope.getNotifications();
					$scope.val['Person'][resourceIndex] = $.extend($scope.val['Person'][resourceIndex], data.plain());
					
					setResourceStatus(resourceId, 'default');
					$scope.showSaveButton[resourceId] = false;
					$scope.showCancelButton[resourceId] = false;
				})
		);
	}
	
	// Function to patch only the changed attributes of a Resource
	$scope.patch = function(resourceId){
		var resourceIndex = _getResourceIndex(resourceId, $scope.val['Person']);
		patches = diff($scope.initialVal['Person'][resourceIndex], $scope.val['Person'][resourceIndex]) // determine patches
		console.log('not yet implemented');
	}
	
	
	
	// Function to add item to array of scalar
	$scope.addItem = function(obj, property, selected, resourceId){
		if(selected.value != ''){
			if(obj[property] === null) obj[property] = [];
			obj[property].push(selected.value);
			selected.value = '';
			$scope.put(resourceId);
		}else{
			console.log('Empty value selected');
		}
	}
	
	//Function to remove item from array of scalar
	$scope.removeItem = function(obj, key, resourceId){
		obj.splice(key, 1);
		$scope.put(resourceId);
	}
	
	
	
	function _getResourceIndex(itemId, items){
		var index;
		items.some(function(item, idx){
			return (item.id === itemId) && (index = idx)
		});
		return index;
	}
	
	//show/hide save button
	function showHideButtons(invariantRulesHold, requestType, resourceId){
		if(invariantRulesHold && requestType == 'feedback'){
			$scope.showSaveButton[resourceId] = true;
			$scope.showCancelButton[resourceId] = true;
			setResourceStatus(resourceId, 'warning');
		}else if(invariantRulesHold && requestType == 'promise'){
			$scope.showSaveButton[resourceId] = false;
			$scope.showCancelButton[resourceId] = false;
			setResourceStatus(resourceId, 'success');
			$timeout(function(){
				setResourceStatus(resourceId, 'default');
			}, 3000);
		}else{
			setResourceStatus(resourceId, 'danger');
			$scope.showSaveButton[resourceId] = false;
			$scope.showCancelButton[resourceId] = true;
		}
	}
	
	function setResourceStatus(resourceId, status){
		$scope.resourceStatus[resourceId] = { 'warning' : false
											 , 'danger'	 : false
											 , 'default' : false
											 , 'success' : false
											 };
		$scope.resourceStatus[resourceId][status] = true; // set new status
	}
});