async function getFirebaseConfig() {
    return {
        apiKey: $.decrypt($.cookie('XSRF-TOKEN-AK')),
        authDomain: $.decrypt($.cookie('XSRF-TOKEN-AD')),
        databaseURL: $.decrypt($.cookie('XSRF-TOKEN-DU')),
        projectId: $.decrypt($.cookie('XSRF-TOKEN-PI')),
        storageBucket: $.decrypt($.cookie('XSRF-TOKEN-SB')),
        messagingSenderId: $.decrypt($.cookie('XSRF-TOKEN-MS')),
        appId: $.decrypt($.cookie('XSRF-TOKEN-AI')),
        measurementId: $.decrypt($.cookie('XSRF-TOKEN-MI'))
    };
}

async function initializeFirebase() {
    const firebaseConfig = await getFirebaseConfig();
    
    if (!firebaseConfig.apiKey || !firebaseConfig.authDomain) {
        console.error("Firebase configuration is missing or invalid.");
        return;
    }

    firebase.initializeApp(firebaseConfig);
}

async function authenticateFirebase() {
    try {

        await storeJsonFile();
        
        const firebaseToken = $.cookie('firebase_token');

        if (firebaseToken) {
            await firebase.auth().signInWithCustomToken(firebaseToken);
        } else {
            const response = await fetch('/get-firebase-token', {
                method: 'POST',
                credentials: 'include',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });
            if (!response.ok) throw new Error("Failed to fetch Firebase token");

            const data = await response.json();
            
            if (!data.firebase_token) throw new Error("Firebase token is missing");
            
            $.cookie('firebase_token', data.firebase_token, { expires: 1/24 });

            await firebase.auth().signInWithCustomToken(data.firebase_token);
        }
        
    } catch (error) {
        console.error("Authentication Error:", error.message);
    }
}

async function storeJsonFile() {
    return new Promise((resolve, reject) => {
        firebase.firestore().collection('settings').doc("notification_setting").get().then(async function (snapshots) {
            var data = snapshots.data();
            if (data != undefined && data.serviceJson != '' && data.serviceJson != null) {
                try {
                    await $.ajax({
                        type: 'POST',
                        data: {
                            serviceJson: btoa(data.serviceJson),
                        },
                        url: "/store-firebase-service",
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function (response) {
                            resolve(response);
                        },
                        error: function (error) {
                            reject(error);
                        }
                    });
                } catch (error) {
                    reject(error);
                }
            } else {
                resolve();
            }
        }).catch((error) => {
            reject(error);
        });
    });
}

initializeFirebase().then(authenticateFirebase);
