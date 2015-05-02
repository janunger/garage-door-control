'use strict';

angular.module('doorApp', [])
    .controller('DoorStatusCtrl', function ($scope, $http, $timeout) {
        var loadDoorState = function () {
            $http.
                get('/state/current.json').
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
                    $scope.statusDate = data.date + ' ';
                    if (data.ageOfStateSeconds > 5) {
                        $scope.statusClass = 'status-unknown';
                        $scope.statusDate = $scope.statusDate + ' (' + data.ageOfStateSeconds + ' Sekunden alt)';
                    }

                    $timeout(loadDoorState, 1000);
                }).
                error(function () {
                    $scope.statusMessage = 'FEHLER';
                    $scope.statusClass = 'error';
                    $scope.statusDate = new Date();

                    $timeout(loadDoorState, 1000);
                });
        };

        $scope.statusMessage = 'wird geladen';
        $scope.statusClass = 'status-unknown';
        $scope.statusDate = new Date();

        loadDoorState();
    });
