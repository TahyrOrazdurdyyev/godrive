const admin = require('firebase-admin');
const serviceAccount = require('./credentials.json'); // Adjust the path if needed
const projectId = serviceAccount.project_id;
console.log("projectId", projectId);

admin.initializeApp({
    credential: admin.credential.cert(serviceAccount),
    databaseURL: "https://${projectId}-default-rtdb.firebaseio.com" // Replace with your database URL
});

const firestore = admin.firestore();

/*
** Auto cancel ride after 24 hours
*/
async function autoRideCancelCron() {
   
    const dateToMilliSec = admin.firestore.Timestamp.now().toMillis();
    const yesterdayDate = new Date(dateToMilliSec - (24 * 60 * 60 * 1000));
    // Ride cancel for orders
    const ordersSnapshot = await firestore.collection('orders').where('status', '==', 'Ride Placed').where('createdDate', '<', yesterdayDate).get();
    if(ordersSnapshot.size > 0) {
        ordersSnapshot.forEach(function(doc) {
            const orderData=doc.data();
            //console.log(orderData);
            if (!orderData) {
                console.log("No order data");
                return;
            }
            console.log('orders : orderId: '+orderData.id);
            firestore.collection('orders').doc(orderData.id).update({
                status: "Ride Canceled",
                acceptedDriverId: []
            });
        });
    }else{
        console.log("No results found for orders");
    }

    // Ride cancel for intercity orders   

    const currentDate = new Date(); // Current timestamp

    const ordersIntercitySnapshot = await firestore.collection('orders_intercity').where('status', '==', 'Ride Placed').get();
    
    if (ordersIntercitySnapshot.size > 0) {
        ordersIntercitySnapshot.forEach(function(doc) {
            const orderData = doc.data();
            if (!orderData) {
                console.log("No order data");
                return;
            }
    
            const whenDateTime = new Date(orderData.whenDates + ' ' + orderData.whenTime); // Ride date and time
            const whenDateTimeStamp = whenDateTime.getTime(); // Ride timestamp
            const currentTimeStamp = currentDate.getTime(); // Current timestamp
            const timeDifference = currentTimeStamp - whenDateTimeStamp; // Difference in milliseconds
            
           /* console.log('orders_intercity : orderId: ' + orderData.id + ' : whenDateTime: ' + whenDateTime + ' : currentDate: ' + currentDate);
            console.log('when date time: ' + whenDateTimeStamp + ' : current time: ' + currentTimeStamp);
            console.log('Time difference (ms): ' + timeDifference + ' : 24 hours in ms: ' + (24 * 60 * 60 * 1000));
            */
            // Check if the ride was placed more than 24 hours ago
            if (timeDifference > (24 * 60 * 60 * 1000)) {
                //console.log('Ride is older than 24 hours, canceling...');
                firestore.collection('orders_intercity').doc(orderData.id).update({
                    status: "Ride Canceled",
                    acceptedDriverId: []
                }).then(() => {
                    console.log('Ride canceled for orderId:', orderData.id);
                }).catch((error) => {
                    console.error('Error canceling ride:', error);
                });
            } else {
                console.log('Ride is within the last 24 hours, no cancellation');
            }
        });
    } else {
        console.log("No results found for intercity orders");
    }

};

autoRideCancelCron();
