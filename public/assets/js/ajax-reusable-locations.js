/*
NOTE:
targetSelect, province, municipality, barangay - are all <select> ID in the Form
code = reg_code, phcode_prov etc.
loadLocationsCodes function will fetch all data in the specific location, barangay is required
*/
function loadRegions(targetSelect) {
    return $.ajax({
        url: '/getRegions',
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            let reg = '<option value="">-- Select Region --</option>';

            data.forEach(region => {
                reg += `<option value="${region.reg_code}">
                                ${region.reg_name}
                            </option>`;
            });

            $('#'+targetSelect).html(reg);
        },
        error: function() {
            alert('Failed to load regions');
        }
    });
}

function getProvince(code,province,municipality,barangay) {
    let reg_code = code;

    let prov = '<option value="">-- Select Province --</option>';
    let mun = '<option value="">-- Select Municipality --</option>';
    let bgy = '<option value="">-- Select Barangay --</option>';
    
    return $.ajax({
        url: '/getProvinces',
        type: 'GET',
        data: { reg_code: reg_code },
        dataType: 'json',
        success: function(provinces) {
            provinces.forEach(p => {
                prov += `<option value="${p.prov_code}">${p.prov_name}</option>`;
            });
            $('#'+province).html(prov);
            $('#'+municipality).html(mun);
            $('#'+barangay).html(bgy);
        },
        error: function() {
            alert('Failed to load provinces');
        }
    });
}

function getMunicipality(code,municipality,barangay) {
    let prov_code = code;

    let mun = '<option value="">-- Select Municipality --</option>';
    let bgy = '<option value="">-- Select Barangay --</option>';

    return $.ajax({
        url: '/getMunicipalities',
        type: 'GET',
        data: { prov_code: prov_code },
        dataType: 'json',
        success: function(provinces) {
            provinces.forEach(p => {
                mun += `<option value="${p.prov_code+'_'+p.mun_code}">${p.mun_name}</option>`;
            });
            $('#'+municipality).html(mun);
            $('#'+barangay).html(bgy);
        },
        error: function() {
            alert('Failed to load municipalities');
        }
    });
};

function getBarangay(code,barangay) {
    let codes = code;
    var locString = codes.split("_"); 
    var prov_code = locString[0];
    var mun_code = locString[1];

    let bgy = '<option value="">-- Select Barangay --</option>';
    
    return $.ajax({
        url: '/getBarangays',
        type: 'GET',
        data: { prov_code: prov_code, mun_code: mun_code },
        dataType: 'json',
        success: function(provinces) {
            provinces.forEach(p => {
                bgy += `<option value="${p.bgy_code}">${p.bgy_name}</option>`;
            });
            $('#'+barangay).html(bgy);
        },
        error: function() {
            alert('Failed to load barangays');
        }
    });
};

// Load full location data by geo_code
function loadLocationCodes(geo_code) {
    return $.ajax({
        url: '/getLocationCodes',
        type: 'GET',
        data: { geo_code: geo_code },
        dataType: 'json',
        success: function(data) {
            currentLocationData = data; // store for reuse
            console.log('Location data:', currentLocationData);
        },
        error: function() {
            alert('Failed to load location data');
        }
    });
}

///// to be used to crossmatch data from select dropdowns to farmers/farm database location codes
function loadLocationIDs(region, province, municipality, barangay) {
    return $.ajax({
        url: '/getLocationIDs',
        type: 'GET',
        data: { reg_code: region, phcode_prov: province, phcode_mun: municipality, geo_code: barangay },
        dataType: 'json',
        success: function(data) {
            currentLocationData = data; // store for reuse
            //console.log('Location data:', currentLocationData);
        },
        error: function() {
            alert('Failed to load location data');
        }
    });
}

// Example usage: get full location data for a selected barangay
// $('#barangay').on('change', function() {
//     let geo_code = $(this).val();
//     if (geo_code) {
//         loadLocationCodes(geo_code);
//     }
// });
