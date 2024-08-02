import React, { useState, useEffect } from 'react';
import axios from 'axios';
import Header from '../components/Header.jsx';
import Footer from '../components/Footer.jsx';
import { Button } from "@/components/ui/button"


const Status = () => {
    const [driverInfo, setDriverInfo] = useState(null);

    const [isAvailable, setIsAvailable] = useState(true);

    const [bookings, setBookings] = useState([]);

    const [booking, setBooking] = useState(null);
    const [status, setStatus] = useState('pending');


    const token = sessionStorage.getItem('jwToken');

    useEffect(() => {
        const fetchDriverInfo = async () => {
            
            try {        
                const response = await axios.get('http://localhost:8000/api/driverstatus.php', {
                    headers: { Authorization: `Bearer ${token}` }
                });
                console.log("response data ", response.data);
                setDriverInfo(response.data);
                setIsAvailable(response.data.is_available);
                setBookings(response.data.bookings || []);

            } catch (error) {
                console.error('Error fetching driver info:', error);
            }
        };

        fetchDriverInfo();
    }, []);


    useEffect(() => {
        const fetchbookingstatus = async () => {
            try {
    
                const response = await axios.get('http://localhost:8000/api/bookingstatus.php', {
                    headers: { Authorization: `Bearer ${token}` }
                });
                console.log(response.data);
                setBooking(response.data);
                setStatus(response.data.status);
            } catch (error) {
                console.error('Error fetching driver info:', error);
            }
        };

        fetchbookingstatus();
    }, [token]);

    const handleStatusChange = async (newStatus) => {
        try {
            const response = await axios.put('http://localhost:8000/api/bookingstatus.php', {
                status: newStatus,
            }, {
                headers: { Authorization: `Bearer ${token}` }
            });
            
            // Assuming the backend responds with the updated booking data
            setStatus(response.data.status);
        } catch (error) {
            console.error('Error updating booking status:', error);
        }
    };







    const handleAvailabilityChange = async () => {

        const newAvailability = !isAvailable;

        try {
            const token = sessionStorage.getItem('jwToken');
            await axios.put('http://localhost:8000/api/driverstatus.php', {
                is_available: newAvailability
            }, {
                headers: { Authorization: `Bearer ${token}` }
            });
            setIsAvailable(newAvailability);
        } catch (error) {
            console.error('Error updating availability:', error);
        }
    };



    return (
        <div className="flex flex-col min-h-screen">
            <Header />

            <div className="flex flex-col items-center flex-grow justify-center rounded-lg bg-white lg:pb-0">
                <h1 className="text-xl text-center font-anta text-black pt-4">Driver Status</h1>
                {driverInfo ? (

                    
                    <div>
                        <p>Name: {driverInfo.name}</p>
                        <p>Status: {isAvailable ? 'Available' : 'Not Available'}</p>
                        <button onClick={handleAvailabilityChange} className="bg-black text-white rounded-md px-4 py-2 mt-4">
                            {isAvailable ? 'Mark as Not Available' : 'Mark as Available'}
                        </button>
                    </div>
                ) : (
                    <p>Loading...</p>
                )}
            </div>



            <div>
            {booking ? (
                <div>
                    <h2>Booking Details</h2>
                    <p>Name: {booking.name}</p>
                    <p>Pickup: {booking.pickup}</p>
                    <p>Dropoff: {booking.dropoff}</p>
                    <p>Status: {status}</p>
                    { status === 'pending' && (  
                    <div>
                    <Button className="mx-12" onClick={() => handleStatusChange('accepted')}>Accept</Button>
                    <Button onClick={() => handleStatusChange('rejected')}>Reject</Button>
                    </div>
                    )}
                </div>
            ) : (
                <p>No Pending Bookings Right Now ...</p>
            )}
        </div>

            <Footer />
        </div>
    );
};

export default Status;
