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
        ['label' => 'Chemicals', 'icon' => 'beaker', 'route' => 'chemicals.index'],
        ['label' => 'Chemical Requests', 'icon' => 'truck', 'route' => 'chemical_requests.index'],
        ['label' => 'Farmers', 'icon' => 'user', 'route' => 'farmers.index'],
        ['label' => 'Farmer Groups', 'icon' => 'users', 'route' => 'farmer_groups.index'],
        ['label' => 'Cooperatives', 'icon' => 'users', 'route' => 'cooperatives.index'],
    ],
    'codapecrep' => [
        ['label' => 'Warehouse Stocks', 'icon' => 'beaker', 'route' => 'warehouse_stocks.index'],
        ['label' => 'Chemical Requests', 'icon' => 'truck', 'route' => 'rep.chemical_requests.index'],
        ['label' => 'Chemical Dispatch', 'icon' => 'truck', 'route' => 'dispatches.index'],
    ],
    'dco' => [
        ['label' => 'Dispatches', 'icon' => 'truck', 'route' => 'dco.dispatches.index'],
        ['label' => 'Distribution List', 'icon' => 'users', 'route' => 'dco.distributions.index'],
        ['label' => 'Distribute to Farmers', 'icon' => 'user', 'route' => 'dco.distribute.farmers'],
        ['label' => 'Distribute to Farmer Groups', 'icon' => 'users', 'route' => 'dco.distribute.farmer_groups'],
        ['label' => 'Distribute to Cooperatives', 'icon' => 'users', 'route' => 'dco.distribute.cooperatives'],
    ],
    'auditor' => [
        ['label' => 'Dispatches', 'icon' => 'truck', 'route' => 'auditor.dispatches.index'],
    ],
    'regional_manager' => [
        ['label' => 'Dispatches', 'icon' => 'truck', 'route' => 'rm.dispatches.index'],
    ],
];
