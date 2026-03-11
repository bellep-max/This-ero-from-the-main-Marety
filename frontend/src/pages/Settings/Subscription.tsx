import React, { useState, useEffect, useMemo } from 'react';
import apiClient from '@/api/client';
import { SETTINGS, SUBSCRIPTIONS } from '@/api/endpoints';
import SettingsLayout from '@/Layouts/SettingsLayout';

export default function SettingsSubscription() {
  const [subscription, setSubscription] = useState<any>(null);
  const [plans, setPlans] = useState<any[]>([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    const fetchData = async () => {
      try {
        const response = await apiClient.get(SETTINGS.SUBSCRIPTION);
        const apiData = response.data;
        setSubscription(apiData.subscription ?? null);
        setPlans(apiData.plans ?? []);
      } catch (error) {
        console.error('Failed to load page data:', error);
      } finally {
        setLoading(false);
      }
    };
    fetchData();
  }, []);

  const handleSuspend = async () => {
    try {
      await apiClient.post(SUBSCRIPTIONS.SUSPEND);
      setSubscription((prev: any) => prev ? { ...prev, status: 'suspended' } : prev);
    } catch (error) {
      console.error('Failed to suspend subscription:', error);
    }
  };

  const handleActivate = async () => {
    try {
      await apiClient.post(SUBSCRIPTIONS.ACTIVATE);
      setSubscription((prev: any) => prev ? { ...prev, status: 'active' } : prev);
    } catch (error) {
      console.error('Failed to activate subscription:', error);
    }
  };

  const handleCancel = async () => {
    try {
      await apiClient.post(SUBSCRIPTIONS.CANCEL);
      setSubscription(null);
    } catch (error) {
      console.error('Failed to cancel subscription:', error);
    }
  };

  if (loading) {
    return (
      <div className="bg-gradient-default py-3 p-md-5 p-lg-6 min-vh-100 d-flex justify-content-center align-items-center">
        <div className="spinner-border text-light" role="status">
          <span className="visually-hidden">Loading...</span>
        </div>
      </div>
    );
  }

  const subscriptionActionName = subscription?.status === 'active'
    ? 'Suspend Subscription'
    : 'Activate Subscription';

  return (
    <SettingsLayout title="Subscription">
      <div className="d-flex flex-column justify-content-center align-items-center gap-4">
        {subscription ? (
          <>
            <button
              className="btn btn-pink btn-wide"
              onClick={subscription.status === 'active' ? handleSuspend : handleActivate}
            >
              {subscriptionActionName}
            </button>
            <button className="btn btn-pink btn-wide" onClick={handleCancel}>
              Cancel Subscription
            </button>
          </>
        ) : (
          <>
            <div className="font-default fs-5 fw-bolder">
              Subscribe to unlock premium features
            </div>
            <div className="text-start font-default fs-14">
              <p>- Unlimited uploads</p>
              <p>- Priority support</p>
              <p>- Advanced analytics</p>
              <p>- Ad-free experience</p>
              <p>- Exclusive content</p>
            </div>
            <a href={SUBSCRIPTIONS.CHECKOUT} className="btn btn-pink">
              Subscribe Now
            </a>
          </>
        )}
      </div>
    </SettingsLayout>
  );
}
