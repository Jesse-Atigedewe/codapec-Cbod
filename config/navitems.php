<?php

return [
    'superadmin' => [
        ['label' => 'User List', 'icon' => 'users', 'route' => 'users.index'],
    ],
    'admin' => [
        ['label' => 'User List', 'icon' => 'users', 'route' => 'users.index'],
        ['label' => 'Warehouse List', 'icon' => 'users', 'route' => 'warehouses.list'],
        ['label' => 'Haulage Companies', 'icon' => 'beaker', 'route' => 'haulage_companies.index'],
        ['label' => 'Region List', 'icon' => 'map', 'route' => 'regions.index'],
        ['label' => 'District List', 'icon' => 'map-pin', 'route' => 'districts.index'],
        ['label' => 'Request List', 'icon' => 'map-pin', 'route' => 'requests.index'],
    ['label' => 'Input', 'icon' => 'beaker', 'route' => 'chemicals.index'],
    ['label' => 'Input Types', 'icon' => 'beaker', 'route' => 'chemical_types.index'],
        ['label' => 'Evacuate', 'icon' => 'truck', 'route' => 'chemical_requests.index'],
        ['label' => 'Farmers', 'icon' => 'user', 'route' => 'farmers.index'],
        // ['label' => 'Farmer Groups', 'icon' => 'users', 'route' => 'farmer_groups.index'],
        ['label' => 'Cooperatives', 'icon' => 'users', 'route' => 'cooperatives.index'],
        ['label' => 'Reports', 'icon' => 'users', 'route' => 'reports.list','badge' => 'unread_reports',],
        
    ],
    'codapecrep' => [
        ['label' => 'Warehouse Stocks', 'icon' => 'beaker', 'route' => 'warehouse_stocks.index'],
        ['label' => 'Evacuations', 'icon' => 'truck', 'route' => 'rep.chemical_requests.index'],
        ['label' => 'Chemicals Evacuated', 'icon' => 'truck', 'route' => 'dispatches.index'],
    ],
    'dco' => [
        ['label' => 'Dispatches', 'icon' => 'truck', 'route' => 'dco.dispatches.index'],
        ['label' => 'Distribution List', 'icon' => 'users', 'route' => 'dco.distributions.index'],
        //dco requests
        ['label' => 'Requests', 'icon' => 'users', 'route' => 'requests.index'],

        //list farmers, farmer groups, cooperatives
          ['label' => 'Farmers', 'icon' => 'user', 'route' => 'farmers.index'],
        // ['label' => 'Farmer Groups', 'icon' => 'users', 'route' => 'farmer_groups.index'],
        ['label' => 'Cooperatives', 'icon' => 'users', 'route' => 'cooperatives.index'],
    ],
    'auditor' => [
        ['label' => 'Dispatches', 'icon' => 'truck', 'route' => 'auditor.dispatches.index'],
    ],
    'regional_manager' => [
        ['label' => 'Dispatches', 'icon' => 'truck', 'route' => 'rm.dispatches.index'],
        ['label' => 'Requests', 'icon' => 'users', 'route' => 'requests.index'],
    ],
];
