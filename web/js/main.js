$(window).on("load", function() {

    let group = {};
    let selectedGroupId = 0;
    let selectedSpecId = 0;
    let objGroups;
    let objGroupsSpecs;
    let objStudents;
    let objTable;
    let logFormName = '#logForm';
    let tableName = '.zebra';

    connectLoginPage();

    /*--------------------- Events ---------------------*/
    $("#group").change(function () {
        $("#group option:selected").each(function () {
            let selectedGroupName = $(this).html();
            getSelectedGroupId(selectedGroupName);
            let groupSpecs = getSpecs();
            setSpecSelectors(groupSpecs);
        });
    });

    $("#get-stat").click(function (e) {
        e.preventDefault();
        getCheckedSpec();
        getTable();
    });

    function setLogInClickHandler() {
        $("#log-in").click(function (e) {
            e.preventDefault();
            logIn(logFormName);
        });
    }

    function setLogOutClickHandler() {
        $("#log-out").click(function (e) {
            e.preventDefault();
            logOut();
        });
    }

    /*--------------------- Senders ---------------------*/
    function connect() {
        showOverlay();

        $.ajax({
            type: "POST",
            url: "web/connection/pull",
            data: "getGroupSpec=1",
            timeout: 5100,
            success: function (data) {
                hideOverlay();

                // Authorize user if response of logbook API is positive
                if (data === 'User not found') {
                    unlogUser();
                    return;
                } else {
                    showAuthorizedPanel();
                    setLogOutClickHandler();

                    try {
                        data = data.split("|||");
                        objGroups = JSON.parse(data[0]);
                        objGroupsSpecs = JSON.parse(data[1]);
                        getGroups();
                    } catch (e) {
                        console.log(e);
                    }
                }
            }
        }).fail(() => {
            unlogUser();
            hideOverlay();
        });
    }

    function getTable() {
        showOverlay();

        $.post("web/connection/pull", "groupId=" + selectedGroupId + "&groupSpec=" + selectedSpecId, function (data) {
            hideOverlay();
            data = data.split("|||");
            objStudents = JSON.parse(data[0]);
            objTable = JSON.parse(data[1]);
            createTable(objStudents, objTable);
        }).fail(() => {
            alert('Невозможно получить оценки. Нет данных.');
            hideOverlay();
        });
    }

    /*--------------------- Login functions ---------------------*/
    function connectLoginPage(postData) {
        $.ajax({
            type: "POST",
            url: "web/auth/login",
            data: postData,
            timeout: 5100,
            success: function(data) {
                // Auth data is set in session
                if (data !== '' && data !== 'exit') {
                    connect();
                } else {
                    showUnauthorizedPanel();
                    clearGroupSpecSelectors();
                    setLogInClickHandler();
                }
            },
            error: function(jqXHR, textStatus){
                if(textStatus === 'timeout') {
                    console.log('Не удалось получить данные из сессии из-за внутреннего таймаута.');
                }
            }
        });
    }

    function unlogUser() {
        alert('Невозможно залогиниться на удаленном сервере. Проверьте правильность логина и пароля.');
        logOut();
        showUnauthorizedPanel();
        clearGroupSpecSelectors();
        setLogInClickHandler();
    }

    function logIn(obj) {
        let error = false;

        $(obj).find('.required').each(function() {
            if ($(this).val() == '') {
                alert('Вы не заполнили поле "' + $(this).attr('name')+'"!');
                error = true;
            }
        });

        if (error == false) {
            let arr = $(obj).serialize();
            connectLoginPage(arr + "&enter=true");
        }
    }

    function logOut() {
        connectLoginPage("exit=true");
        hideInfo();
    }
    
    function showAuthorizedPanel() {
        $('.top-nav').html('');
        $('.top-nav').html('<div class="nav"><ul><li>Добро пожаловать!</li><li><a id="log-out" href="#">Выйти</a></li></ul></div>');

        hideStartInfo();
    }

    function showUnauthorizedPanel() {
        $('.top-nav').html('');
        $('.top-nav').append('<form id="logForm"></form>');
        $('.top-nav form').append('<div class="form-row align-items-center"></div>');
        $('.top-nav form>div').append('<div class="col"></div>');
        $('.top-nav form>div').append('<div class="col"></div>');
        $('.top-nav form>div').append('<div class="col-auto"></div>');
        $('.top-nav form>div>div:nth-child(1)').append('<input type="text" class="form-control log-form required" name="login" placeholder="login">');
        $('.top-nav form>div>div:nth-child(2)').append('<input type="password" class="form-control log-form required" name="password" placeholder="password">');
        $('.top-nav form>div>div:nth-child(3)').append('<button type="submit" id="log-in" class="btn btn-primary log-form ">Войти</button>');

         showStartInfo();
    }

    /*--------------------- Main statistic logic ---------------------*/
    function getGroups() {
        for (let i in objGroups) {
            group[objGroups[i].name_tgroups] = objGroups[i].id_tgroups;
        }
        setGroupSelectors();
    }

    function setGroupSelectors() {
        $("#group").html('<option selected>Выберите группу...</option>');
        for (let key in group) {
            $("#group").append('<option>' + key + '</option>');
        }
    }

    function setSpecSelectors(groupSpecs) {
        $("#spec").html('<option selected>Выберите предмет...</option>');
        for (let key in groupSpecs) {
            $("#spec").append('<option>' + key + '</option>');
        }
    }

    function clearGroupSpecSelectors() {
        $("#group").html('<option selected>Выберите группу...</option>');
        $("#spec").html('<option selected>Выберите предмет...</option>');
        $(tableName).html('');
    }

    function getSelectedGroupId(selectedGroupName) {
        selectedGroupId = group[selectedGroupName];
    }

    function getSpecs() {

        let groupSpecs = {};
        let specifiedGroupSpecs = objGroupsSpecs.groups_spec[selectedGroupId].data;

        for (let i in specifiedGroupSpecs) {
            groupSpecs[specifiedGroupSpecs[i].name_spec] = specifiedGroupSpecs[i].id_spec;
        }

        return groupSpecs;
    }

    function getCheckedSpec() {
        let specName = $("#spec option:checked").html();
        let groupSpecs = getSpecs();
        selectedSpecId = groupSpecs[specName];
    }

    function createTable(students, tableMarks) {

        let table = [];
        let marks = tableMarks.data_body;
        let headers = tableMarks.data_header;
        let minColumns = 6;

        // Creating single student row
        for (let i in students) {
            let row = [];
            let fioStud = students[i].fio_stud;
            let studentDates = marks[students[i].id_stud];
            let groupDates = headers;

            row.push(fioStud + ': ');

            // Fixing a bug with duplicating marks
            let listOfDates = [];
            let previousDateStr = '';
            for (let i in groupDates) {
                let currentDateStr = groupDates[i].date_vizit;
                if (currentDateStr != previousDateStr) listOfDates.push(currentDateStr);
                previousDateStr = currentDateStr;
            }

            // Adding marks
            for (let date in listOfDates) {
                let lessons;

                // Fixing a bug with students who were are added in group later
                // Adding additional dashes
                try {
                    lessons = studentDates[listOfDates[date]];
                    if (typeof lessons === 'undefined') {
                        row.push('-');
                        row.push('-');
                    };

                    for (let lesNum in lessons) {
                        let lessonMark = lessons[lesNum].mark4;
                        let homeWorkMark = lessons[lesNum].home_work;
                        let wasAtTheLesson = lessons[lesNum].was;

                        if (wasAtTheLesson == '0') {
                            lessonMark = 'н';
                            if (homeWorkMark == null) homeWorkMark = '-';
                        }
                        else {
                            if (lessonMark == null) lessonMark = '-';
                            if (homeWorkMark == null) homeWorkMark = '-';
                        }

                        row.push(lessonMark);
                        row.push(homeWorkMark);
                    }
                } catch (e) {
                    // Removing old student records
                    row = [];
                }
            }

            // Row length check. The length must be the same for all students
            let cellNumber = headers.length * 2 + 1;

            if (cellNumber > row.length && row.length > 0) {
                let addCells = headers.length * 2 - row.length;

                for (let i = 0; i <= addCells; i++) {
                    row.push('н');
                }
            }

            // Calculations for statistic
            // Sending a raw row for calculating HW
            let homeWorks = calculateAccomplishedHW(row);
            let accomplishedHW = homeWorks[0]
            let unperformedHW = homeWorks[1];
            let averageCurrent = calculateAverageCurrent(row);
            let averageAll = calculateAverageAll(row);
            let quantity = calculateQuantity(row);

            row.push(averageCurrent);
            row.push(averageAll);
            row.push(quantity);
            row.push(accomplishedHW);
            row.push(unperformedHW);

            // Ignoring empty rows of old student records
            // The number depends on a quantity of additional column in the end of a table
            if (row.length > minColumns) table.push(row);
        }

        let tableHeader = getTableHeader(headers);
        buildTable(table, tableHeader);
    }

    function getTableHeader(headers) {
        let tableHeader = [];

        for (let i in headers) {
            tableHeader.push(headers[i].date_vizit);
            tableHeader.push(headers[i].date_vizit);
        }

        tableHeader.push("Среднее, тек.");
        tableHeader.push("Среднее, все");
        tableHeader.push("Кол-во оценок");
        tableHeader.push("Выполн. дз");
        tableHeader.push("Невыполн. дз");

        return tableHeader;
    }

    function calculateAccomplishedHW(numArr) {
        let accomplishedHW = 0;
        let unperformedHW = 0;
        let homeWorks = [];

        for (let i in numArr) {
            if (i > 0 && i%2 == 0)  {
                if (numArr[i] != "-") {
                    accomplishedHW++;
                } else {
                    unperformedHW++;
                }
            }
        }

        homeWorks.push(accomplishedHW);
        homeWorks.push(unperformedHW);
        return homeWorks;
    }

    function calculateAverageCurrent(numArr) {
        let sum = 0;
        let counter = 0;

        for (let i in numArr) {
            if (numArr[i] != "н") {
                if (i > 0 && numArr[i] != "-") {
                    sum += parseInt(numArr[i]);
                    counter++;
                }
            }
        }

        let averageMark = Math.round(sum / counter);

        return averageMark;
    }

    function calculateAverageAll(numArr) {
        let sum = 0;
        let counter = 0;

        for (let i in numArr) {

            if (numArr[i] == "н") {
                counter++;
            }
            else {
                if (i > 0) {
                    if (numArr[i] == "-") {
                        sum += 1;
                    }
                    else {
                        sum += parseInt(numArr[i]);
                    }
                }
            }
        }

        let averageMark = Math.round(sum / (numArr.length - counter - 1));

        return averageMark;
    }

    function calculateQuantity(numArr) {
        let quantity = 0;

        for (let i in numArr) {
            if (i > 0 && numArr[i] != "-")  quantity++;
        }

        //Fixing a problem with two additional rows of average marks
        quantity -= 2;

        return quantity;
    }

    function buildTable(table, tableHeader) {
        $(tableName).html('');
        $(tableName).append('<tr></tr>');
        $(tableName + " tr:nth-child(1)").append('<th>ФИО</th>');

        for (let i in tableHeader) {
            $(tableName + " tr:nth-child(1)").append('<th><svg width="12" height="110" class="svg-text"><text x="-100" y="10" transform="rotate(-90)">' + tableHeader[i] + '</text></svg></th>');
        }

        for (let i in table) {
            let row = table[i];
            let incNum = parseInt(i)+2;

            $(tableName).append('<tr></tr>');

            for (let j in row) {
                if ((row.length - j) < 6) {
                    $(tableName + " tr:nth-child(" + incNum + ")").append('<td class="summary">' + row[j] + '</td>');
                } else {
                    if (row[j] == 'н') {
                        $(tableName + " tr:nth-child(" + incNum + ")").append('<td class="miss">' + row[j] + '</td>');
                    }
                    else {
                        $(tableName + " tr:nth-child(" + incNum + ")").append('<td>' + row[j] + '</td>');
                    }
                }
            }
        }

        showInfo();
    }

    function showOverlay() {
        $("#overlay").css({"display":"block"});
    }

    function hideOverlay() {
        $("#overlay").css({"display":"none"});
    }

    function showInfo() {
        $("#info").css({"display":"block"});
    }

    function hideInfo() {
        $("#info").css({"display":"none"});
    }

    function showStartInfo() {
        $("#start-info").css({"display":"block"});
    }

    function hideStartInfo() {
        $("#start-info").css({"display":"none"});
    }

    function cons(data) {
        console.log(data);
    }

});