Certainly! Here's an improved version of your README:

# Funnel RESTful API for WordPress (FunnelPress)

**Plugin Version: 3.0.0**

FunnelPress is a powerful WordPress plugin designed for headless WordPress backends. It empowers developers to create email and phone number funnels on their websites.

## What is a Funnel?

In the context of pop-up elements on a website, a funnel refers to a structured series of pop-ups or overlays strategically designed to guide website visitors through a specific conversion path or customer journey. The primary goal of a pop-up funnel is to encourage users to take desired actions, such as signing up for a newsletter, making a purchase, or completing a form.

**Note: This plugin is designed to be used in a headless WordPress environment.**

## Key Features

FunnelPress offers a range of features to enhance your website's user engagement and data collection capabilities:

- **Creation and Editing**: Easily create and edit funnels from your WordPress admin panel.
- **Browsing Data**: Browse data collected from your funnels, either by a specific funnel or across all funnels.
- **RESTful API**: Empower your frontend to collect emails or phone numbers through a RESTful API.

## Planned Enhancements

We have exciting plans for FunnelPress's future development:

- **Email Service**: Automate email communication with users based on admin-defined criteria.
- **SMS Service**: Implement an SMS service to send automatic messages to users as determined by the admin.

## Usage

To interact with FunnelPress, you can use the following namespace:

```
http(s)://example.com/wp-json/funnel
```

### GET currentFunnel

Retrieve information about the current active funnel:

```
http(s)://example.com/wp-json/funnel/current
```

A successful response will resemble the following JSON structure:

```json
{
    "status": "success",
    "data": {
        "id": "2",
        "message": "workout_plan",
        "active": true,
        "phone": true,
        "hero_image": [...],
        "header_icon": [...],
        "header_text": "WORKOUT PLAN",
        "header_subtext": "Take this free workout plan for all students in any discipline of martial arts",
        "button_text": "GET PLAN!"
    }
}
```

If there is no active funnel, you will receive the following response:

```json
{
	"status": "error",
	"data": "No active funnel element"
}
```

### POST submit

When a user decides to submit their information, use the following endpoint:

```
http://localhost:8080/wordpress/wp-json/funnel/submit
```

The expected request body in TypeScript format is:

```typescript
{
	funnel_id: number,
	funnel_message: string,
	email?: string,
	phonenumber?: string
}
```

A successful response may look like this:

```json
{
	"status": "success",
	"message": "Email submitted"
}
```

In case of a failure, you will receive an error message:

```json
{
	"status": "error",
	"message": "No number or email set"
}
```

## WordPress Panel

FunnelPress offers some basic settings through the WordPress admin panel:

- **Create Element**: Create new funnels easily.
- **Manage and View Elements**: Edit and manipulate existing funnels and browse data specific to a funnel.
- **View Funnel Data**: Access all collected funnel data, regardless of the funnel. You can find FunnelPress on the admin dashboard sidebar, represented by a graph icon.