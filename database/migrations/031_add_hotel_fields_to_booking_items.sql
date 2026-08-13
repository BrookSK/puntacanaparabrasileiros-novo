-- Migration: Adicionar campos de hotel e pickup ao booking_items
-- Data: 2026-08-12

ALTER TABLE booking_items
ADD COLUMN hotel_name VARCHAR(255) NULL AFTER trip_time,
ADD COLUMN pickup_time VARCHAR(10) NULL AFTER hotel_name;
