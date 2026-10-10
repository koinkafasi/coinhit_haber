@extends('errors.layout')

@section('title', __('ui.error_500_title'))
@section('code', '500')
@section('heading', __('ui.error_500_heading'))
@section('message', __('ui.error_500_message'))