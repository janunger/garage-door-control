'use strict';

angular.module('doorApp', [])
    .controller('DoorCtrl', function ($scope, $http, $timeout) {
        var loadState = function () {
            var timestamp = (new Date()).getTime();
            $http.
                get('/state/current.json?' + timestamp).
                success(function (data) {
                    switch (data.doorState) {
                        case 'closed':
                            $scope.statusMessage = 'geschlossen';
                            $scope.statusClass = 'status-closed';
                            break;
                        case 'opened':
                            $scope.statusMessage = 'offen';
                            $scope.statusClass = 'status-opened';
                            break;
                        case 'unknown':
                            $scope.statusMessage = 'in Bewegung';
                            $scope.statusClass = 'status-unknown';
                            break;
                        default:
                            $scope.statusMessage = 'FEHLER';
                            $scope.statusClass = 'status-error';
                    }

                    $scope.statusDate = data.date + '';
                    $scope.currentSequence = data.autoSequence;
                    var ageOfState = Math.abs(Math.round(((new Date(data.date)).getTime() - timestamp)/1000));
                    if (ageOfState > 30) {
                        $scope.statusClass = 'status-error';
                        $scope.statusDate = $scope.statusDate + ' (' + ageOfState + ' Sekunden Differenz)';
                    }

                    $timeout(loadState, 1000);
                }).
                error(function () {
                    $scope.statusMessage = 'FEHLER';
                    $scope.statusClass = 'status-error';
                    $scope.statusDate = new Date();
                    $scope.currentSequence = null;

                    $timeout(loadState, 1000);
                });
        };

        $scope.isRequestRunning = false;
        $scope.isButtonTriggerDisabled = function () {
            return $scope.isRequestRunning;
        };
        $scope.isButtonSequenceDisabled = function () {
            return $scope.isRequestRunning || null !== $scope.currentSequence;
        };
        $scope.isButtonCancelDisabled = function () {
            return $scope.isRequestRunning || null === $scope.currentSequence;
        };
        $scope.sendTriggerCommand = function () {
            $scope.isRequestRunning = true;
            $http.post('/trigger.php', {}).
                success(function (data, status, headers, config) {
                    $scope.isRequestRunning = false;
                });
        };
        $scope.sendSequenceOneCommand = function () {
            $scope.isRequestRunning = true;
            $http.post('/trigger.php', {sequence: 1}).
                success(function (data, status, headers, config) {
                    $scope.isRequestRunning = false;
                });
        };
        $scope.sendSequenceTwoCommand = function () {
            $scope.isRequestRunning = true;
            $http.post('/trigger.php', {sequence: 2}).
                success(function (data, status, headers, config) {
                    $scope.isRequestRunning = false;
                });
        };
        $scope.sendCancelCommand = function () {
            $scope.isRequestRunning = true;
            $http.post('/trigger.php', {'cancel_sequence': true}).
                success(function (data, status, headers, config) {
                    $scope.isRequestRunning = false;
                });
        };

        $scope.videostreamUrl = '';
        $scope.clickVideostream = function () {
            $scope.videostreamUrl = '/videostream';
        };

        $scope.statusMessage = 'wird geladen';
        $scope.statusClass = 'status-unknown';
        $scope.statusDate = new Date();
        $scope.currentSequence = null;

        loadState();
    });
