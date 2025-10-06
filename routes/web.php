<?php

use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Profile;
use App\Livewire\User\CreateUser;
use App\Livewire\District\DistrictList;
         use App\Livewire\Regions\CreateRegion;
        use App\Livewire\Regions\EditRegion;
        use App\Livewire\District\DistrictCreate;
        use App\Livewire\District\DistrictEdit;
use App\Livewire\User\EditUser;
use App\Livewire\User\ListUsers;
use App\Livewire\Warehouse\CreateWarehouse;
use App\Livewire\Warehouse\EditWarehouse;
use App\Livewire\Warehouse\ListWarehouse;
use App\Livewire\Regions\ListRegions;
         use App\Livewire\Districts\CreateDistrict;
        use App\Livewire\Districts\EditDistrict;
        use App\Livewire\Districts\ListDistricts;
         use App\Livewire\HaulageCompanies\CreateHaulageCompany;
        use App\Livewire\HaulageCompanies\EditHaulageCompany;
        use App\Livewire\HaulageCompanies\ListHaulageCompanies;
         use App\Livewire\Chemicals\CreateChemical;
        use App\Livewire\Chemicals\EditChemical;
        use App\Livewire\Chemicals\ListChemicals;
         use App\Livewire\WarehouseStocks\CreateWarehouseStock;
        use App\Livewire\WarehouseStocks\ListWarehouseStocks;
         use App\Livewire\ChemicalRequests\CreateChemicalRequest;
        use App\Livewire\ChemicalRequests\EditChemicalRequest;
        use App\Livewire\ChemicalRequests\ListChemicalRequests;
         use App\Livewire\Farmers\CreateFarmer;
        use App\Livewire\Farmers\EditFarmer;
        use App\Livewire\Farmers\ListFarmers;
         use App\Livewire\FarmerGroups\CreateFarmerGroup;
        use App\Livewire\FarmerGroups\EditFarmerGroup;
        use App\Livewire\FarmerGroups\ListFarmerGroups;
         use App\Livewire\Cooperatives\CreateCooperative;
        use App\Livewire\Cooperatives\EditCooperative;
        use App\Livewire\Cooperatives\ListCooperatives;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Route::get('settings/profile', Profile::class)->name('settings.profile');
    Route::get('settings/password', Password::class)->name('settings.password');
    Route::get('settings/appearance', Appearance::class)->name('settings.appearance');
});


    Route::middleware(['auth', 'role:superadmin,admin'])->group(function () {
        Route::get('/users', ListUsers::class)->name('users.index');
        Route::get('/users/{user}', EditUser::class)->name('users.edit');
        Route::get('/users/create/user', CreateUser::class)->name('create-users.create');

      
    });
    Route::middleware(['auth', 'role:admin,codapecrep,dco,regional_manager,auditor'])->group(function () {
    Route::get('/dispatches/{record}/info', \App\Livewire\Dispatches\ViewDispatch::class)
        ->name('dispatches.info');
    });
    
    Route::middleware(['auth', 'role:admin,codapecrep,dco,regional_manager,auditor'])->group(function () {
        //farmers, farmergroup and cooperative routes 
              Route::get('/farmers', ListFarmers::class)->name('farmers.index');
              Route::get('/farmer-groups', ListFarmerGroups::class)->name('farmer_groups.index');
              Route::get('/cooperatives', ListCooperatives::class)->name('cooperatives.index');
        //get details of a farmer group members
        Route::get('/farmer-groups/{record}/member', \App\Livewire\Distribution\ViewFarmerGroupMember::class)
        ->name('listfarmergroupmember');

         Route::get('/farmer/{record}/member', \App\Livewire\Distribution\ViewFarmerMember::class)
        ->name('listfarmermember');

         Route::get('/cooperative/{record}/member', \App\Livewire\Distribution\ViewCooperativeMember::class)
        ->name('listcooperativemember');
    
    
    });



    Route::middleware(['auth', 'role:admin'])->group(function () {
        Route::get('/warehouses', ListWarehouse::class)->name('warehouses.list');
        // Warehouse CRUD routes for testing
        Route::get('/warehouses/create', CreateWarehouse::class)->name('warehouse.create');
        Route::get('/warehouses/{warehouse}/edit', EditWarehouse::class)->name('warehouses.edit');
          // Warehouse CRUD
          Route::get('/warehouses', ListWarehouse::class)->name('warehouses.list');
          Route::get('/warehouses/create', CreateWarehouse::class)->name('warehouse.create');
          Route::get('/warehouses/{warehouse}/edit', EditWarehouse::class)->name('warehouses.edit');
  
         //Region
          Route::get('/regions', ListRegions::class)->name('regions.index');
          Route::get('/regions/create', CreateRegion::class)->name('regions.create');
          Route::get('/regions/{record}/edit', EditRegion::class)->name('regions.edit');
          // District CRUD
          Route::get('/districts', ListDistricts::class)->name('districts.index');
          Route::get('/districts/create', CreateDistrict::class)->name('districts.create');
          Route::get('/districts/{record}/edit', EditDistrict::class)->name('districts.edit');
          // Haulage Companies
          Route::get('/haulage-companies', ListHaulageCompanies::class)->name('haulage_companies.index');
          Route::get('/haulage-companies/create', CreateHaulageCompany::class)->name('haulage_companies.create');
          Route::get('/haulage-companies/{record}/edit', EditHaulageCompany::class)->name('haulage_companies.edit');
          // Chemicals
          Route::get('/chemicals', ListChemicals::class)->name('chemicals.index');
          Route::get('/chemicals/create', CreateChemical::class)->name('chemicals.create');
          Route::get('/chemicals/{record}/edit', EditChemical::class)->name('chemicals.edit');
          // Chemical Types (CRUD)
          Route::get('/chemical-types', \App\Livewire\ChemicalTypes\ListChemicalTypes::class)->name('chemical_types.index');
          Route::get('/chemical-types/create', \App\Livewire\ChemicalTypes\CreateChemicalType::class)->name('chemical_types.create');
          Route::get('/chemical-types/{record}/edit', \App\Livewire\ChemicalTypes\EditChemicalType::class)->name('chemical_types.edit');
          // Dispatch approvals (admin manage)
          Route::get('/dispatches', \App\Livewire\Dispatches\ListDispatches::class)->name('dispatches.index');
          Route::get('/dispatches/{record}/edit', \App\Livewire\Dispatches\EditDispatch::class)->name('dispatches.edit');
          // Chemical Requests
          Route::get('/admin/chemical-requests', ListChemicalRequests::class)->name('chemical_requests.index');
          Route::get('/admin/chemical-requests/create', CreateChemicalRequest::class)->name('chemical_requests.create');
          Route::get('/admin/chemical-requests/{record}/edit', EditChemicalRequest::class)->name('chemical_requests.edit');
          // Farmers
        //   Route::get('/farmers', ListFarmers::class)->name('farmers.index');
          Route::get('/farmers/create', CreateFarmer::class)->name('farmers.create');
          Route::get('/farmers/{record}/edit', EditFarmer::class)->name('farmers.edit');
          // Farmer Groups
        //   Route::get('/farmer-groups', ListFarmerGroups::class)->name('farmer_groups.index');
          Route::get('/farmer-groups/create', CreateFarmerGroup::class)->name('farmer_groups.create');
          Route::get('/farmer-groups/{record}/edit', EditFarmerGroup::class)->name('farmer_groups.edit');
          // Cooperatives
        //   Route::get('/cooperatives', ListCooperatives::class)->name('cooperatives.index');
          Route::get('/cooperatives/create', CreateCooperative::class)->name('cooperatives.create');
          Route::get('/cooperatives/{record}/edit', EditCooperative::class)->name('cooperatives.edit');
          
       
    });

    Route::middleware(['auth', 'role:codapecrep'])->group(function () {
          // Warehouse Stock (CODAPEC rep only)
          Route::get('/warehouse-stocks', ListWarehouseStocks::class)->name('warehouse_stocks.index');
          Route::get('/warehouse-stocks/create', CreateWarehouseStock::class)->name('warehouse_stocks.create');
          // Chemical Requests (CODAPEC rep scoped)
          Route::get('/chemical-requests', ListChemicalRequests::class)->name('rep.chemical_requests.index');
          Route::get('/chemical-requests/{record}/edit', EditChemicalRequest::class)->name('rep.chemical_requests.edit');
          Route::get('/codapec/dispatches', \App\Livewire\Dispatches\ListDispatches::class)->name('dispatches.index');
            //Dispatches
          Route::get('/codapec/dispatches/create', \App\Livewire\Dispatches\CreateDispatches::class)->name('dispatches.create');
          Route::get('/codapec/dispatches/{record}/edit', \App\Livewire\Dispatches\EditDispatch::class)->name('dispatches.edit');

        });

    Route::middleware(['auth', 'role:dco'])->group(function () {
          // DCO sees dispatches to approve and distributions
          Route::get('/dco/dispatches', \App\Livewire\Dispatches\ListDispatches::class)->name('dco.dispatches.index');
        //   Route::get('/dispatches/{record}/edit', \App\Livewire\Dispatches\EditDispatch::class)->name('dco.dispatches.edit');
          // Distributions
          Route::get('/distribute/farmers', \App\Livewire\DcoDistributions\DistributeToFarmers::class)->name('dco.distribute.farmers');
          Route::get('/distribute/farmer-groups', \App\Livewire\DcoDistributions\DistributeToFarmerGroups::class)->name('dco.distribute.farmer_groups');
          Route::get('/distribute/cooperatives', \App\Livewire\DcoDistributions\DistributeToCooperatives::class)->name('dco.distribute.cooperatives');
          Route::get('/dco/distributions', \App\Livewire\DcoDistribution\ListDcoDistributions::class)->name('dco.distributions.index');

        });

    Route::middleware(['auth', 'role:auditor'])->group(function () {
          // Auditor sees dispatches to approve
          Route::get('/auditor/dispatches', \App\Livewire\Dispatches\ListDispatches::class)->name('auditor.dispatches.index');
        //   Route::get('/dispatches/{record}/edit', \App\Livewire\Dispatches\EditDispatch::class)->name('auditor.dispatches.edit');
    });

    Route::middleware(['auth', 'role:regional_manager'])->group(function () {
          // Regional manager approves and triggers DCO received record
          Route::get('/regional-manager/dispatches', \App\Livewire\Dispatches\ListDispatches::class)->name('rm.dispatches.index');
        //   Route::get('/dispatches/{record}/edit', \App\Livewire\Dispatches\EditDispatch::class)->name('rm.dispatches.edit');
    });

Route::middleware(['auth', 'role:superadmin'])->group(function () {
    Route::get('/settings', function () {
        return view('settings.index');
    })->name('settings.index');
});

require __DIR__.'/auth.php';
