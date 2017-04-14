var app = angular.module('OpenITService' , []);

app.config(function ($interpolateProvider)
{
    $interpolateProvider.startSymbol('[[');
    $interpolateProvider.endSymbol(']]');
});

app.directive('fileModel' , ['$parse' , function ($parse)
{
    return {
        restrict: 'A' ,
        link    : function (scope , element , attrs)
        {
            var model = $parse(attrs.fileModel);
            var modelSetter = model.assign;
            element.bind('change' , function ()
            {
                scope.$apply(function ()
                {
                    modelSetter(scope , element[0].files);
                });
            });
        }
    };
}]);

app.service('fileUpload' , ['$http' , function ($http)
{

    this.uploadFileToUrl = function (file , uploadUrl , call)
    {
        var fd = new FormData();
        if ( file.length > 1 )
        {
            for ( var i = 0; i < file.length; i++ )
            {
                fd.append('files[]' , file[i]);
            }
        }
        else
        {
            fd.append('file[]' , file[0]);
        }
        var result = false;

        return $http({
            method          : 'POST' ,
            url             : uploadUrl ,
            data            : fd ,
            transformRequest: angular.identity ,
            headers         : {'Content-Type': undefined}
        }).then(function success(response)
        {
            call(response.data);
        } , function error(response)
        {
            UIkit.notify("Невозможно загрузить данное изображение!" , {
                pos    : 'top-right' ,
                status : 'danger' ,
                timeout: 2000
            });

        });
    }
}]);

app.service('Translate' , function ()
{
    this.RuEn = function (text)
    {

        if ( text )
        {
            text = text.toLowerCase();
            var translit_table = {
                "а": "a" , "ый": "iy" , "ые": "ie" ,
                "б": "b" , "в": "v" , "г": "g" ,
                "д": "d" , "е": "e" , "ё": "yo" ,
                "ж": "zh" , "з": "z" , "и": "i" ,
                "й": "y" , "к": "k" , "л": "l" ,
                "м": "m" , "н": "n" , "о": "o" ,
                "п": "p" , "р": "r" , "с": "s" ,
                "т": "t" , "у": "u" , "ф": "f" ,
                "х": "kh" , "ц": "ts" , "ч": "ch" ,
                "ш": "sh" , "щ": "shch" , "ь": "" ,
                "ы": "y" , "ъ": "" , "э": "e" ,
                "ю": "yu" , "я": "ya" , "йо": "yo" ,
                "ї": "yi" , "і": "i" , "є": "ye" ,
                "ґ": "g"
            };

            var ignor = {
                ",": "-" , ".": "-" ,
                ":": "-" , " ": "-" , "<": "-" ,
                ">": "-" , "#": "-" , "@": "-" ,
                "?": "-" , "*": "-" , "%": "-" ,
                "(": "-" , ")": "-"
            };
            var res = '';
            for ( var i = 0; i < text.length; i++ )
            {
                if ( ignor[text[i]] )
                {
                    if ( ignor[text[i - 1]] != '-' )
                        res += ignor[text[i]];
                }
                else
                {
                    if ( (text[i].charCodeAt() >= 1072 && text[i].charCodeAt() <= 1103) )
                    {
                        res += translit_table[text[i]];
                    }
                    else
                    {
                        res += text[i];
                    }
                }
            }
            if ( res[res.length - 1] == '-' )
                res = res.substring(0 , res.length - 1);
            return res;
        }
        return '';
    };
});

app.service('Parser' , function ()
{
    this.Url = function (e)
    {
        var temp = e.split("/");
        var url_param = [];
        var id = [];
        for ( var i = 0; i < temp.length; i++ )
        {
            if ( temp[i] != "" && temp[i] !== 'http:' && temp[i] !== 'https:' )
            {
                url_param[url_param.length] = temp[i];
            }
        }

        temp = url_param[url_param.length - 1].split("-");
        for ( i = 0; i < temp.length; i++ )
        {
            if ( temp[i] != "" )
            {
                id[id.length] = temp[i];
            }
        }
        url_param[url_param.length - 1] = id[id.length - 1];
        return url_param;
    };
});


app.service('Validate' , function (messages)
{
    this.validate = function (object , rules)
    {
        var result = true;
        for ( var i in rules )
        {
            if ( object[i] === '' || object[i] === null )
            {
                messages.Error('Поле ' + i + ' не может быть пустым');
                console.log(object[i]);
                result = result * false;
            }
        }
        return result;
    };
});

app.service('messages' , function ()
{
    this.Success = function (message)
    {
        UIkit.notify('<i class="uk-icon-check"></i>' + message , {status: 'success'})
    };

    this.Error = function (message)
    {
        UIkit.notify('<i class="uk-icon-close"></i>' + message , {status: 'danger'})
    };

    this.Warning = function (message , sleep)
    {
        if ( sleep == undefined )
        {
            sleep = 3000;
        }

        UIkit.notify('<i class="uk-icon-close"></i>' + message , {status: 'warning' , timeout: sleep});


    };
});

app.controller('EventCalendarCtrl' , function ($scope , messages , Translate , $http)
{

    $scope.DayList = {
        'Monday'   : 'Понедельник' ,
        'Tuesday'  : 'Вторник' ,
        'Wednesday': 'Среда' ,
        'Thursday' : 'Четверг' ,
        'Friday'   : 'Пятница' ,
        'Saturday' : 'Суббота' ,
        'Sunday'   : 'Воскресенье' ,
    };

    $scope.DateStart = false;
    $scope.DateEnd = false;
    $scope.modal = UIkit.modal("#my-id");
    $scope.Event = {reminders: [] , channels: []};
    $scope.Events = [];
    $scope.Month = false;
    $scope.Year = false;
    $scope.EventID = false;
    $scope.CurrentDate = false;
    $scope.ChannelList = [];
    $scope.AddChannelID = false;

    $scope.$watch('Month && Year' , function (e)
    {
        if ( $scope.Month && $scope.Year )
        {
            $scope.getEvents();
        }
    });

    $scope.getEvents = function ()
    {
        $http({
            method: 'POST' ,
            url   : '/admin/events/get/calendar' ,
            data  : {
                'Month': $scope.Month ,
                'Year' : $scope.Year
            }
        }).then(function success(response)
            {
                $scope.Events = response.data;
            } ,
            function error(response)
            {

            });
    };

    $scope.getChanenlList = function (id)
    {
        $http({
            method: 'GET' ,
            url   : '/admin/event/get/channels/' + id
        }).then(function success(response)
        {
            $scope.ChannelList = response.data;

        } , function error(response)
        {

        });
    };

    $scope.addChannel = function ()
    {
        if ( $scope.AddChannelID )
        {
            $http({
                method: 'POST' ,
                url   : '/admin/event/add/channel/' + $scope.EventID ,
                data  : {
                    'channel_id': $scope.AddChannelID
                }
            }).then(function success(response)
            {
                $scope.ShowEvent($scope.EventID);

            } , function error(response)
            {

            });
        }
    };

    $scope.addChannelNew = function ()
    {
        console.log($scope.ChannelList[$scope.AddChannelID]);
        console.log($scope.AddChannelID);

        if ( $scope.AddChannelID )
        {
            $scope.Event.channels.push({
                channel: {
                    "id"  : $scope.ChannelList[$scope.AddChannelID].id ,
                    "name": $scope.ChannelList[$scope.AddChannelID].name
                }
            });
            $scope.ChannelList.splice($scope.AddChannelID , 1);
            $scope.AddChannelID = false;
        }
    };

    $scope.ShowEvent = function (id)
    {
        $scope.getChanenlList(id);
        $http({
            method: 'POST' ,
            url   : '/admin/event/get' ,
            data  : {
                'id': id
            }
        }).then(function success(response)
            {
                $scope.Event = response.data;
                $scope.Event.city = $scope.Event.city.toString();
                $scope.Event.event_type = $scope.Event.event_type.toString();
                for ( var i = 0; i < $scope.Event.reminders.length; i++ )
                {
                    var sd = $scope.Event.reminders[i].send_date.split(" ");
                    console.log(sd);
                    $scope.Event.reminders[i].send_date = sd[0];
                    $scope.Event.reminders[i].send_time = sd[1];
                    $scope.Event.reminders[i].active = ($scope.Event.reminders[i].active ? true : false);
                }
                $scope.modal.show();
                $scope.EventID = id;

            } ,
            function error(response)
            {

            });
    };

    $scope.ShowAddEvent = function (date)
    {
        $scope.EventID = false;
        $scope.Event = {reminders: [] , channels: []};
        $scope.getChanenlList($scope.EventID);
        $scope.Event.the_date = date;
        $scope.modal.show();
    };

    $scope.saveEvent = function ()
    {
        if ( $scope.EventID )
        {
            for ( var key in $scope.Event.reminders )
            {
                if ( $scope.Event.reminders.hasOwnProperty(key) )
                {

                    $scope.Event.reminders[key].send_date = $scope.Event.reminders[key].send_date + ' ' + $scope.Event.reminders[key].send_time;
                }
            }
            $http({
                method: 'POST' ,
                url   : '/admin/event/save/' + $scope.EventID ,
                data  : {
                    'data': $scope.Event
                }
            }).then(function success(response)
                {
                    $scope.getEvents();
                    $scope.EventID = false;
                    messages.Success('Событие успешно сохренено!');
                    $scope.modal.hide();
                } ,
                function error(response)
                {
                    messages.Error('Событие не может быть сохранено!');

                });
        }
    };


    $scope.addEvent = function ()
    {
        if ( $scope.Event.name && $scope.Event.content )
        {

            for ( var key in $scope.Event.reminders )
            {
                if ( $scope.Event.reminders.hasOwnProperty(key) )
                {
                    $scope.Event.reminders[key].send_date = $scope.Event.reminders[key].send_date + ' ' + $scope.Event.reminders[key].send_time;
                }
            }

            $http({
                method: 'POST' ,
                url   : '/admin/event/add' ,
                data  : {
                    'data': $scope.Event
                }
            }).then(function success(response)
                {
                    $scope.getEvents();
                    $scope.EventID = false;
                    $scope.Event = {reminders: [] , channels: []};
                    messages.Success('Событие успешно добавлено!');
                    $scope.modal.hide();
                } ,
                function error(response)
                {
                    messages.Error('Событие не может быть добавлено!');

                });
        }
        else
        {
            if ( !$scope.Event.name )
            {
                messages.Warning('Необходимо заполнить поле "Зазвание" ');
            }
            if ( !$scope.Event.content )
            {
                messages.Warning('Необходимо заполнить поле "Контент" ');
            }
        }
    };

    $scope.deleteEvent = function ()
    {
        $http({
            method: 'DELETE' ,
            url   : '/admin/event/delete/' + $scope.EventID
        }).then(function success(response)
            {
                $scope.EventID = false;
                $scope.Event = {reminders: [] , channels: []};
                $scope.modal.hide();
                $scope.getEvents();

                messages.Success('Событие успешно удалено!');
            } ,
            function error(response)
            {
                messages.Error('Событие не может быть удалено!');

            });
    };

    $scope.AddReminder = function (id)
    {
        $scope.Event.reminders.push({
            "event_id" : id ,
            "send_date": '' ,
            "done"     : 0 ,
            "active"   : 1
        });
    };

    $scope.deleteChannel = function (id)
    {

        $http({
            method: 'DELETE' ,
            url   : '/admin/event/channel/delete/' + id
        }).then(function success(response)
            {
                $scope.ShowEvent($scope.EventID);
                messages.Success('Канал успешно удален!');
            } ,
            function error(response)
            {
                messages.Error('Канал не может быть удален!');

            });

    };

    $scope.deleteChannelNew = function (key)
    {
        $scope.ChannelList.push({
            "id"  : $scope.Event.channels[key].channel.id ,
            "name": $scope.Event.channels[key].channel.name
        });
        $scope.Event.channels.splice(key , 1);
    };

    $scope.deleteReminder = function (id)
    {
        $http({
            method: 'DELETE' ,
            url   : '/admin/reminder/delete/' + id
        }).then(function success(response)
            {
                $scope.ShowEvent($scope.EventID);
                messages.Success('Напоминание успешно удалено!');
            } ,
            function error(response)
            {
                messages.Error('Напоминание не может быть удалено!');

            });
    };


});


app.controller('EventsListCtrl' , function ($scope , messages , Translate , $http)
{
    $scope.Events = [];
    $scope.modal = UIkit.modal("#my-id");
    $scope.Event = {reminders: [] , channels: []};
    $scope.AddChannelID = false;

    $scope.getEvents = function ()
    {
        $http({
            method: 'POST' ,
            url   : '/admin/events/get/list'
        }).then(function success(response)
            {
                $scope.Events = response.data;
            } ,
            function error(response)
            {

            });
    };

    $scope.getEvents();

    $scope.ShowEvent = function (id)
    {
        $http({
            method: 'POST' ,
            url   : '/admin/event/get' ,
            data  : {
                'id': id
            }
        }).then(function success(response)
            {
                $scope.Event = response.data;
                $scope.Event.city = $scope.Event.city.toString();
                $scope.Event.event_type = $scope.Event.event_type.toString();
                for ( var i = 0; i < $scope.Event.reminders.length; i++ )
                {
                    var sd = $scope.Event.reminders[i].send_date.split(" ");
                    $scope.Event.reminders[i].send_date = sd[0];
                    $scope.Event.reminders[i].send_time = sd[1];
                    $scope.Event.reminders[i].active = ($scope.Event.reminders[i].active ? true : false);
                }
                $scope.modal.show();
                $scope.EventID = id;

            } ,
            function error(response)
            {

            });
    };

    $scope.ShowAddEvent = function (date)
    {
        $scope.EventID = false;
        $scope.Event = {reminders: [] , channels: []};
        $scope.Event.the_date = date;
        $scope.modal.show();
    };

    $scope.addChannel = function ()
    {
        if ( $scope.AddChannelID )
        {
            $http({
                method: 'POST' ,
                url   : '/admin/event/add/channel/' + $scope.EventID ,
                data  : {
                    'channel_id': $scope.AddChannelID
                }
            }).then(function success(response)
            {
                $scope.ShowEvent($scope.EventID);

            } , function error(response)
            {

            });
        }
    };

    $scope.addChannelNew = function ()
    {
        console.log($scope.ChannelList[$scope.AddChannelID]);
        console.log($scope.AddChannelID);

        if ( $scope.AddChannelID )
        {
            $scope.Event.channels.push({
                channel: {
                    "id"  : $scope.ChannelList[$scope.AddChannelID].id ,
                    "name": $scope.ChannelList[$scope.AddChannelID].name
                }
            });
            $scope.ChannelList.splice($scope.AddChannelID , 1);
            $scope.AddChannelID = false;
        }
    };

    $scope.saveEvent = function ()
    {
        if ( $scope.EventID )
        {
            for ( var key in $scope.Event.reminders )
            {
                if ( $scope.Event.reminders.hasOwnProperty(key) )
                {

                    $scope.Event.reminders[key].send_date = $scope.Event.reminders[key].send_date + ' ' + $scope.Event.reminders[key].send_time;
                }
            }
            $http({
                method: 'POST' ,
                url   : '/admin/event/save/' + $scope.EventID ,
                data  : {
                    'data': $scope.Event
                }
            }).then(function success(response)
                {
                    $scope.getEvents();
                    $scope.EventID = false;
                    messages.Success('Событие успешно сохренено!');
                    $scope.modal.hide();
                } ,
                function error(response)
                {
                    messages.Error('Событие не может быть сохранено!');

                });
        }
    };

    $scope.addEvent = function ()
    {
        if ( $scope.Event.name && $scope.Event.content )
        {

            for ( var key in $scope.Event.reminders )
            {
                if ( $scope.Event.reminders.hasOwnProperty(key) )
                {
                    $scope.Event.reminders[key].send_date = $scope.Event.reminders[key].send_date + ' ' + $scope.Event.reminders[key].send_time;
                }
            }

            $http({
                method: 'POST' ,
                url   : '/admin/event/add' ,
                data  : {
                    'data': $scope.Event
                }
            }).then(function success(response)
                {
                    $scope.getEvents();
                    $scope.EventID = false;
                    $scope.Event = {reminders: [] , channels: []};
                    messages.Success('Событие успешно добавлено!');
                    $scope.modal.hide();
                } ,
                function error(response)
                {
                    messages.Error('Событие не может быть добавлено!');

                });
        }
        else
        {
            if ( !$scope.Event.name )
            {
                messages.Warning('Необходимо заполнить поле "Зазвание" ');
            }
            if ( !$scope.Event.content )
            {
                messages.Warning('Необходимо заполнить поле "Контент" ');
            }
        }
    };

    $scope.deleteEvent = function ()
    {
        $http({
            method: 'DELETE' ,
            url   : '/admin/event/delete/' + $scope.EventID
        }).then(function success(response)
            {
                $scope.EventID = false;
                $scope.Event = {reminders: [] , channels: []};
                $scope.modal.hide();
                $scope.getEvents();

                messages.Success('Событие успешно удалено!');
            } ,
            function error(response)
            {
                messages.Error('Событие не может быть удалено!');

            });
    };

    $scope.AddReminder = function (id)
    {
        $scope.Event.reminders.push({
            "event_id" : id ,
            "send_date": '' ,
            "done"     : 0 ,
            "active"   : 1
        });
    };

    $scope.deleteReminder = function (id)
    {
        $http({
            method: 'DELETE' ,
            url   : '/admin/reminder/delete/' + id
        }).then(function success(response)
            {
                $scope.ShowEvent($scope.EventID);
                messages.Success('Напоминание успешно удалено!');
            } ,
            function error(response)
            {
                messages.Error('Напоминание не может быть удалено!');

            });
    };

    $scope.deleteChannel = function (id)
    {

        $http({
            method: 'DELETE' ,
            url   : '/admin/event/channel/delete/' + id
        }).then(function success(response)
            {
                $scope.ShowEvent($scope.EventID);
                messages.Success('Канал успешно удален!');
            } ,
            function error(response)
            {
                messages.Error('Канал не может быть удален!');

            });

    };

    $scope.deleteChannelNew = function (key)
    {
        $scope.ChannelList.push({
            "id"  : $scope.Event.channels[key].channel.id ,
            "name": $scope.Event.channels[key].channel.name
        });
        $scope.Event.channels.splice(key , 1);
    };

});