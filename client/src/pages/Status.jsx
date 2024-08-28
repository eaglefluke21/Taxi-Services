import React, { useState, useEffect } from 'react';
import axios from 'axios';
import Header from '../components/Header.jsx';
import Footer from '../components/Footer.jsx';
import { Button } from "@/components/ui/button"


const Status = () => {
    const [driverInfo, setDriverInfo] = useState(null);

    const [isAvailable, setIsAvailable] = useState('available');

    const [bookings, setBookings] = useState([]);

    const [booking, setBooking] = useState(null);
    const [status, setStatus] = useState('pending');


    const token = sessionStorage.getItem('jwToken');

    const apiUrl = import.meta.env.VITE_API_URL;
    console.log("current env", apiUrl);

    useEffect(() => {
        const fetchDriverInfo = async () => {
            
            try {        

                const response = await  axios.get(`${apiUrl}/api/driverstatus`, {
                    headers: { Authorization: `Bearer ${token}` }
                });
                console.log("drivers data ", response.data);
                console.log("is available",response.data.is_available);
                console.log("booking",response.data.bookings);
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
    
                const response = await axios.get(`${apiUrl}/api/bookingstatus`, {
                    headers: { Authorization: `Bearer ${token}` }
                });
                 console.log("customer booking data" ,response);
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
            const response = await axios.put(`${apiUrl}/api/bookingstatus`, {
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

        const newAvailability = isAvailable === 'available' ? 'unavailable' : 'available';

        try {
           

            const token = sessionStorage.getItem('jwToken');
            await axios.put(`${apiUrl}/api/driverstatus`, {
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

            <div className="flex flex-col gap-12  sm:flex-row lg:gap-24 items-center flex-grow justify-center rounded-lg bg-white lg:pb-0">
                <div className="flex flex-col items-center justify-center">
                <h1 className="text-xl  font-quick font-bold  lg:3xl ">Driver Status</h1>
                {driverInfo ? (

                    
                    <div className="flex flex-col items-center justify-center " >
                        <p className="font-quick">Name: <span className="font-semibold">{driverInfo.name}</span></p>
                        <p>Status: {isAvailable=== 'available' ? 'Available' : 'Not Available'}</p>
                        <Button onClick={handleAvailabilityChange} className="bg-black w-64 mt-2">
                            {isAvailable  === 'available' ? 'Mark as Not Available' : 'Mark as Available'}
                        </Button>
                    </div>
                ) : (
                    <p className="font-quick font-bold text-md">Loading...</p>
                )}

            </div>
           



            <div >
            {booking ? (
                <div className="flex flex-col items-center justify-center gap-4">
                    <div>
                    <h2 className="font-quick font-bold">Booking Details</h2>
                    <p  className="font-quick">Name: <span className="font-semibold">{booking.name}</span> </p>
                    <p  className="font-quick">Pickup:  <span className="font-semibold"> {booking.pickup}</span> </p>
                    <p  className="font-quick">Dropoff: <span className="font-semibold"> {booking.dropoff}</span> </p>
                    <p  className="font-quick">Status: <span className="font-semibold"> {status}</span> </p>
                    </div>
                    { status === 'pending' && (  
                    <div className='flex flex-col w-64 gap-4'>
                    <Button className="bg-black" onClick={() => handleStatusChange('accepted')}>Accept</Button>
                    <Button className="bg-black" onClick={() => handleStatusChange('rejected')}>Reject</Button>
                    </div>
                    )}
                </div>
            ) : (
                <p className="font-quick font-bold text-md sm:text-xl lg:text-3xl">No Pending Bookings Right Now ...</p>
            )}
        </div>


        </div>


            <Footer />
        </div>
    );
};

export default Status;
