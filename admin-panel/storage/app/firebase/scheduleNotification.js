const admin = require('firebase-admin');
const serviceAccount=require('./credentials.json');
const axios = require('axios');
if (!admin.apps.length) {
    try {
        admin.initializeApp({
            credential: admin.credential.cert(serviceAccount),
            databaseURL: `https://${serviceAccount.project_id}.firebaseio.com`
        });
        console.log('Firebase initialized successfully');
    } catch (error) {
        console.error('Error initializing Firebase:', error);
        process.exit(1);
    }
}

const firestore = admin.firestore();
const messaging = admin.messaging();
var appTimezone='';


    async function sendScheduleNotification() {

        const timingSnapshot = await firestore.collection('settings').doc("schedule_ride_notification").get();
        if (!timingSnapshot.exists) {
            console.log("No notification timing settings found.");
            return;
        }
    
        let timingData = timingSnapshot.data();
        if (!timingData || !timingData.notificationTiming.length) {
            console.log("No notification timing configured.");
            return;
        }
    
        const ordersIntercitySnapshot = await firestore.collection('orders_intercity').where('status', '==', 'Ride Active').get();
    
        for (const doc of ordersIntercitySnapshot.docs) {
            const data = doc.data();

            if (!data || !data.whenDates || !data.whenTime) {
                console.log(`Skipping order ${doc.id} due to missing ride date/time`);
                continue;
            } else {
                const app_url = process.env.APP_URL;
                const response = await axios.get(`${app_url}/api/timezone`);
                const appTimezone = response.data.timezone;
   
                const now = new Date().toLocaleString('en-US', { timeZone: appTimezone });
                const nowDate = new Date(now);

                const selectedDate = new Date(data.whenDates);
                const [hours, minutes] = data.whenTime.split(":").map(Number);
                selectedDate.setHours(hours, minutes, 0, 0);
               
                if (selectedDate.getTime() >= nowDate.getTime()) {

                    const [day, month, year] = data.whenDates.split('-');
                    const months = { Jan: "01", Feb: "02", Mar: "03", Apr: "04", May: "05", Jun: "06", Jul: "07", Aug: "08", Sep: "09", Oct: "10", Nov: "11", Dec: "12" };
            
                    const formattedDate = `${year}-${months[month]}-${day}`;
                    const formattedDateTimeString = `${formattedDate}T${data.whenTime}:00Z`; 
            
                    const rideDateTime = new Date(formattedDateTimeString);
                    if (isNaN(rideDateTime.getTime())) {
                        continue;
                    }            

                    let diffInMs = rideDateTime - nowDate;
                    var diffInMinutes=Math.floor(diffInMs/60000)+1; 
                  
                    for(let {time,unit} of timingData.notificationTiming) {
                    
                            let timeInMinutes;
                            if (unit === "days") {
                                timeInMinutes = time * 1440;
                            } else if (unit === "hours") {
                                timeInMinutes = time * 60;
                            } else if (unit === "minutes") {
                                timeInMinutes = time;
                        }
                       
                          
                            if (diffInMinutes == timeInMinutes) {
                                console.log(`✅ Sending notification for order ${doc.id}`);
                                await sendNotification(data);
                            }
                    } 
                } else{
                    console.log(`these is past order thats why we skip these order ${doc.id}`);
                }
            }
        }
    }

async function sendNotification(orderData) {
   
    try {
        const { driverId, userId, whenDates, whenTime } = orderData;

        const [driverDoc, userDoc] = await Promise.all([
            firestore.collection('driver_users').doc(driverId).get(),
            firestore.collection('users').doc(userId).get()
        ]);

        const driverFcm = driverDoc.exists ? driverDoc.data().fcmToken : null;
        const userFcm = userDoc.exists ? userDoc.data().fcmToken : null;

        // Send notification to user
        if (userFcm) {

            console.log('userFcm', userFcm);
            try {
                const response = await messaging.send({
                    notification: {
                        title: 'Scheduled Ride Reminder',
                        body: `Your ride is scheduled for ${whenDates} at ${whenTime}.`,
                    },
                    token: userFcm
                });
                console.log(`Notification sent to user ${userId}:`, response);
            } catch (error) {
                console.error(`Error sending to user ${userId}:`, error);
            }
        }

        // Send notification to driver
        if (driverFcm) {

            try {
                const response = await messaging.send({
                    notification: {
                        title: 'Scheduled Ride Reminder',
                        body: `Your ride is scheduled for ${whenDates} at ${whenTime}.`,
                    },
                    token: driverFcm
                });
                console.log(`Notification sent to driver ${driverId}:`, response);
            } catch (error) {
                console.error(`Error sending to driver ${driverId}:`, error);
            }
        }
    } catch (error) {
        console.error(`Error in sendNotification:`, error);
    }
}

sendScheduleNotification();
