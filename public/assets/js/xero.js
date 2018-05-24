Handlebars.getTemplate = function (name) {
    if (Handlebars.templates === undefined || Handlebars.templates[name] === undefined) {
        $.get({
            url: 'assets/templates/' + name + '.handlebars',
            success: function (data) {
                if (Handlebars.templates === undefined) {
                    Handlebars.templates = {};
                }
                Handlebars.templates[name] = Handlebars.compile(data);
            },
            async: false
        });
    }
    return Handlebars.templates[name];
};

var endpoint = [
    {name: "Accounts", action: [{name: "Create"}, {name: "Read"}, {name: "Update"}, {name: "Delete"}, {name: "Archive"}]},
    {name: "BankTransactions", action: [{name: "Create"}, {name: "Read"}, {name: "Update"}, {name: "Delete"}]},
    {name: "BankTransfers", action: [{name: "Create"}, {name: "Read"}]},
    {name: "BrandingThemes", action: [{name: "Read"}]},
    {name: "Contacts", action: [{name: "Create"}, {name: "Read"}, {name: "Update"}, {name: "Archive"}]},
    {name: "ContactGroups", action: [{name: "Create"}, {name: "Read"}, {name: "Update"}, {name: "Archive"}]},
    {
        name: "CreditNotes",
        action: [{name: "Create"}, {name: "Read"}, {name: "Update"}, {name: "Delete"}, {name: "Allocate"}, {name: "Refund"}, {name: "Void"}]
    },
    {name: "Currencies", action: [{name: "Read"}]},
    {name: "Employees", action: [{name: "Create"}, {name: "Read"}, {name: "Update"}]},
    {name: "ExpenseClaims", action: [{name: "Create"}, {name: "Read"}, {name: "Update"}]},
    {name: "Invoices", action: [{name: "Create"}, {name: "Read"}, {name: "Update"}, {name: "Delete"}, {name: "Void"}]},
    {name: "InvoiceReminders", action: [{name: "Read"}]},
    {name: "Items", action: [{name: "Create"}, {name: "Read"}, {name: "Update"}, {name: "Delete"}]},
    {name: "Journals", action: [{name: "Read"}]},
    {name: "LinkedTransactions", action: [{name: "Create"}, {name: "Read"}, {name: "Update"}, {name: "Delete"}]},
    {name: "ManualJournals", action: [{name: "Create"}, {name: "Read"}, {name: "Update"}]},
    {name: "Organisations", action: [{name: "Read"}]},
    {name: "Overpayments", action: [{name: "Create"}, {name: "Read"}, {name: "Allocate"}, {name: "Refund"}]},
    {name: "Payments", action: [{name: "Create"}, {name: "Read"}, {name: "Delete"}]},
    {name: "Prepayments", action: [{name: "Create"}, {name: "Read"}, {name: "Allocate"}, {name: "Refund"}]},
    {name: "PurchaseOrders", action: [{name: "Create"}, {name: "Read"}, {name: "Update"}, {name: "Delete"}]},
    {name: "Receipts", action: [{name: "Create"}, {name: "Read"}, {name: "Update"}, {name: "Delete"}]},
    {name: "RepeatingInvoices", action: [{name: "Read"}]},
    {
        name: "Reports",
        action: [{name: "TenNinetyNine"}, {name: "AgedPayablesByContact"}, {name: "AgedReceivablesByContact"}, {name: "BalanceSheet"}, {name: "BankStatement"}, {name: "BankSummary"}, {name: "BudgetSummary"}, {name: "ExecutiveSummary"}, {name: "ProfitAndLoss"}, {name: "TrialBalance"}]
    },
    {name: "TaxRates", action: [{name: "Create"}, {name: "Read"}, {name: "Update"}]},
    {name: "TrackingCategories", action: [{name: "Create"}, {name: "Read"}, {name: "Update"}, {name: "Delete"}, {name: "Archive"}]},
    {name: "TrackingOptions", action: [{name: "Read"}]},
    {name: "Users", action: [{name: "Read"}]}
];

function populateAction(currEndpoint, currAction) {
    for (var i = 0; i < endpoint.length; i++) {
        if (endpoint[i].name == currEndpoint) {
            temp = endpoint[i].action;
        }
    }
    $("#action").children().remove();

    for (var i = 0; i < temp.length; i++) {
        if (temp[i].name == currAction) {
            var selected = 'selected="true"';
        } else {
            var selected = '';
        }

        $("#action").append('<option ' + selected + ' value="' + temp[i].name + '">' + temp[i].name + '</option>');
    }
}

function setArray(arr, match) {
    var len = arr.length;
    for (var i = 0; i < len; i++) {
        if (arr[i].name === match) {
            arr[i].selected = "selected";
        }
    }
}

function loadGet(appName, logoutUrl, currEndpoint, currAction) {

    setArray(endpoint, currEndpoint);

    var template = Handlebars.getTemplate('container');
    $("#req").html(template({
        name: appName,
        logoutUrl: logoutUrl
    }));

    template = Handlebars.getTemplate('options');
    document.querySelector("#endpoint").innerHTML = template(endpoint);

    var action = [{name: "Create"}, {name: "Read"}, {name: "Update"}, {name: "Delete"}];
    document.querySelector("#action").innerHTML = template(action);

    populateAction(currEndpoint, currAction);

    $("#endpoint").on("change", function () {
        if (currAction !== $("#action").val()) {
            currAction = $("#action").val();
        }
        populateAction($("#endpoint").val(), currAction);
    });
}