import React, { useState, useRef } from "react";
import { Animated, Alert } from "react-native";
import { router } from "expo-router";
import axios from "axios";
import axiosInstance from "@/config/axiosConfig";
import {
  RegisterPhases,
  AccountTypeSelection,
  PhoneInput,
  Security,
  YourIdentity,
  PhotoUpload,
  ProfileDetails,
  CalendarSync,
  InfoScreen,
  SuccessScreen,
  phases as registerPhases,
  PHASE_ACCOUNT_TYPE,
  PHASE_PHONE,
  PHASE_SECURITY,
  PHASE_IDENTITY,
  PHASE_PHOTO,
  PHASE_PROFILE,
  PHASE_CALENDAR,
  PHASE_INFO,
  PHASE_SUCCESS,
  type RegisterFormData,
  type AccountType,
} from "@/components/register";

const RegisterWizard = () => {
  // Register wizard state
  const [currentPhase, setCurrentPhase] = useState(0);
  const [isLoading, setIsLoading] = useState(false);
  const [formData, setFormData] = useState<RegisterFormData>({
    accountType: null,
    phone: "",
    email: "",
    password: "",
    fullName: "",
    photo: null,
    businessName: "",
    streetAddress: "",
    hours: "",
    capacity: "",
    website: "",
    instagram: "",
    tiktok: "",
    primaryCity: "",
  });
  
  const lastPressTime = useRef(0);
  const fadeAnim = useRef(new Animated.Value(1)).current;

  const handlePress = (route: string) => {
    const now = Date.now();
    if (now - lastPressTime.current < 1000) return;
    lastPressTime.current = now;
    router.push(route as any);
  };

  const handleGoToLogin = () => {
     router.push("/(auth)/sign-in" as any);
  }

  // Register wizard handlers
  const handleRegisterNext = () => {
    if (currentPhase < registerPhases.length - 1) {
      const nextPhase = currentPhase + 1;
      
      Animated.sequence([
        Animated.timing(fadeAnim, {
          toValue: 0,
          duration: 300,
          useNativeDriver: true,
        }),
        Animated.timing(fadeAnim, {
          toValue: 1,
          duration: 300,
          useNativeDriver: true,
        }),
      ]).start();

      if (nextPhase === PHASE_INFO) {
        // Before showing info screen, submit registration
        handleSignup();
      } else {
        setCurrentPhase(nextPhase);
      }
    } else {
      // Final step - navigate to main app
      handlePress("/(app)");
    }
  };

  const handleSignup = async() => {
    setIsLoading(true);

    try {
      const registrationData = {
        account_type: formData.accountType,
        phone: formData.phone,
        email: formData.email,
        password: formData.password,
        name: formData.fullName,
        photo: formData.photo,
        business_name: formData.businessName,
        street_address: formData.streetAddress,
        hours: formData.hours,
        capacity: formData.capacity,
        website: formData.website,
        instagram: formData.instagram,
        tiktok: formData.tiktok,
        primary_city: formData.primaryCity,
      };

      const response = await axiosInstance.post(`/register`, registrationData);
      
      // Navigate to success phase
      setCurrentPhase(PHASE_SUCCESS);
    } catch (error) {
      if (axios.isAxiosError(error)) {
        const responseData = error.response?.data;
        if (responseData?.errors) {
          try {
            const firstError = Object.values(responseData.errors).flat()[0];
            Alert.alert("Registration Error", String(firstError));
          } catch (e) {
            Alert.alert("Registration Error", JSON.stringify(responseData.errors));
          }
        } else if (responseData?.message) {
          const msg = typeof responseData.message === "string" ? responseData.message : JSON.stringify(responseData.message);
          Alert.alert("Registration Error", msg);
        } else {
          Alert.alert("Registration Error", 'An unexpected error occurred. Please try again.');
        }
      } else {
        console.error("Registration error:", error);
        Alert.alert("Registration Error", 'Unable to connect to the server');
      }
    } finally {
      setIsLoading(false);
    }
  };

  const handleRegisterBack = () => {
    if (currentPhase > 0) {
      const prevPhase = currentPhase - 1;
      
      Animated.sequence([
        Animated.timing(fadeAnim, {
          toValue: 0,
          duration: 300,
          useNativeDriver: true,
        }),
        Animated.timing(fadeAnim, {
          toValue: 1,
          duration: 300,
          useNativeDriver: true,
        }),
      ]).start();

      setCurrentPhase(prevPhase);
    } else {
      // Go back to welcome/theme selection screen
      router.back();
    }
  };

  // Form data updaters
  const updateFormData = (field: keyof RegisterFormData, value: any) => {
    setFormData(prev => ({ ...prev, [field]: value }));
  };

  // Validation for "Next" button
  const canGoNext = () => {
    switch (currentPhase) {
      case PHASE_ACCOUNT_TYPE:
        return formData.accountType !== null;
      case PHASE_PHONE:
        return formData.phone.length >= 10;
      case PHASE_SECURITY:
        return formData.email.length > 0 && formData.password.length > 0;
      case PHASE_IDENTITY:
        return formData.fullName.length > 0;
      case PHASE_PHOTO:
        return true; // Photo is optional
      case PHASE_PROFILE:
        return formData.businessName.length > 0 || formData.accountType !== "business";
      case PHASE_CALENDAR:
        return true; // Calendar is optional
      case PHASE_INFO:
        return true;
      case PHASE_SUCCESS:
        return true;
      default:
        return false;
    }
  };

  // Render current phase content
  const renderPhaseContent = () => {
    switch (currentPhase) {
      case PHASE_ACCOUNT_TYPE:
        return (
          <AccountTypeSelection
            selectedType={formData.accountType}
            onTypeSelect={(type) => updateFormData("accountType", type)}
          />
        );
      
      case PHASE_PHONE:
        return (
          <PhoneInput
            phone={formData.phone}
            onPhoneChange={(phone) => updateFormData("phone", phone)}
          />
        );
      
      case PHASE_SECURITY:
        return (
          <Security
            email={formData.email}
            password={formData.password}
            onEmailChange={(email) => updateFormData("email", email)}
            onPasswordChange={(password) => updateFormData("password", password)}
          />
        );
      
      case PHASE_IDENTITY:
        return (
          <YourIdentity
            fullName={formData.fullName}
            onFullNameChange={(name) => updateFormData("fullName", name)}
          />
        );
      
      case PHASE_PHOTO:
        return (
          <PhotoUpload
            photo={formData.photo}
            onPhotoSelect={() => {
              // TODO: Implement photo picker
              console.log("Photo picker");
            }}
            onSkip={handleRegisterNext}
          />
        );
      
      case PHASE_PROFILE:
        return (
          <ProfileDetails
            businessName={formData.businessName}
            streetAddress={formData.streetAddress}
            hours={formData.hours}
            capacity={formData.capacity}
            website={formData.website}
            instagram={formData.instagram}
            tiktok={formData.tiktok}
            primaryCity={formData.primaryCity}
            onBusinessNameChange={(value) => updateFormData("businessName", value)}
            onStreetAddressChange={(value) => updateFormData("streetAddress", value)}
            onHoursChange={(value) => updateFormData("hours", value)}
            onCapacityChange={(value) => updateFormData("capacity", value)}
            onWebsiteChange={(value) => updateFormData("website", value)}
            onInstagramChange={(value) => updateFormData("instagram", value)}
            onTiktokChange={(value) => updateFormData("tiktok", value)}
            onPrimaryCityChange={(value) => updateFormData("primaryCity", value)}
          />
        );
      
      case PHASE_CALENDAR:
        return (
          <CalendarSync
            onConnect={() => {
              // TODO: Implement calendar connection
              console.log("Connect calendar");
              handleRegisterNext();
            }}
            onSkip={handleRegisterNext}
          />
        );
      
      case PHASE_INFO:
        return <InfoScreen />;
      
      case PHASE_SUCCESS:
        return <SuccessScreen />;
      
      default:
        return null;
    }
  };

  // Render appropriate screen
  return (
    <RegisterPhases
      phases={registerPhases}
      currentPhase={currentPhase}
      fadeAnim={fadeAnim}
      onNext={handleRegisterNext}
      onBack={handleRegisterBack}
      goToLogin={handleGoToLogin}
      canGoNext={canGoNext()}
      isLastPhase={currentPhase === PHASE_SUCCESS}
    >
      {renderPhaseContent()}
    </RegisterPhases>
  );
};

export default RegisterWizard;
