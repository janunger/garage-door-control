'use strict';

angular.module('doorApp', [])
    .controller('DoorStatusCtrl', function ($scope, $http, $timeout) {
        var loadDoorState = function () {
            $http.
                get('/door-state').
                success(function (data) {
                    var statusMessage;
                    var statusClass;

                    switch (data.doorState) {
                        case 'closed':
                            statusMessage = 'geschlossen';
                            statusClass = 'closed';
                            break;
                        case 'opened':
                            statusMessage = 'offen';
                            statusClass = 'opened';
                            break;
                        case 'unknown':
                            statusMessage = 'in Bewegung';
                            statusClass = 'unknown';
                            break;
                        default:
                            statusMessage = 'FEHLER';
                            statusClass = 'error';
                    }

                    $scope.statusMessage = statusMessage;
                    $scope.statusClass = 'status-' + statusClass;
                    $scope.statusDate = new Date();

                    $timeout(loadDoorState, 2000);
                }).
                error(function () {
                    $scope.statusMessage = 'FEHLER';
                    $scope.statusClass = 'status-error';
                    $scope.statusDate = new Date();

                    $timeout(loadDoorState, 2000);
                });
        };

        $scope.statusMessage = 'unbekannt';
        $scope.statusClass = 'status-unknown';
        $scope.statusDate = new Date();

        loadDoorState();
    });
